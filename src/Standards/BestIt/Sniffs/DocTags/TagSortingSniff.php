<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\Helper\DocTagHelper;
use BestIt\CodeSniffer\Helper\LineHelper;
use BestIt\Sniffs\AbstractSniff;
use function array_column;
use function array_shift;
use function implode;
use function preg_match;
use function str_pad;
use function strcasecmp;
use function usort;
use const T_DOC_COMMENT_OPEN_TAG;

/**
 * Checks the sorting and grouping of the doc comment tags.
 *
 * Same tags are grouped. The groups are sorted by occurrence, where the tags which only occure once are collected in
 * one block. Blocks of tags with the same occurrence-count are sorted alphabetically.
 *
 * The return tag comes last, everytime!
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class TagSortingSniff extends AbstractSniff
{
    /**
     * You SHOULD separate tag groups and the final return with a newline.
     */
    const CODE_MISSING_NEWLINE_BETWEEN_TAGS = 'MissingNewlineBetweenTags';

    /**
     * You SHOULD sort the tags by their occurrence and then alphabetically, but @return SHOULD be the last.
     */
    const CODE_WRONG_TAG_SORTING = 'WrongTagSorting';

    /**
     * The message for the missing new line between tags.
     */
    const MESSAGE_MISSING_NEWLINE_BETWEEN_TAGS = 'There should be a newline after the tag (group): %s.';

    /**
     * The message for the wrong sorting order.
     */
    const MESSAGE_WRONG_TAG_SORTING = 'Please provide the tags in occurrence and then alphabetical order 
        (a-z) but with return at last position.';

    /**
     * The doc tag helper.
     *
     * @var DocTagHelper
     */
    private $docTagHelper;

    /**
     * Returns true if the requirements for this sniff are met.
     *
     * @return bool Are the requirements met and the sniff should proceed?
     */
    protected function areRequirementsMet(): bool
    {
        return !$this->isSniffSuppressed() && (bool) $this->getTagTokens();
    }

    /**
     * Checks for line break errors and registers the errors if there are any.
     *
     * This should be called after the sorting is already checked and fixed!
     *
     * @return void
     */
    private function checkAndRegisterLineBreakErrors()
    {
        $tokens = $this->getTagTokens();
        $prevToken = [];
        $tagCounts = $this->docTagHelper->getTagCounts($tokens);
        $withReturn = false;

        foreach ($tokens as $tokenPos => $token) {
            $thisTagContent = $token['content'];
            $isReturn = $thisTagContent === '@return';

            $isGroupSwitch =
                // Did we switch the tag ...
                $prevToken && ($prevToken['content'] !== $thisTagContent) &&
                // ... but do we skip singles or are we entering the return block which should contain only 1 tag.
                (($tagCounts[$thisTagContent] !== 1) || ($isReturn && !$withReturn));

            // Insert new line between groups or before the return tag if there is no line break already.
            if ($isGroupSwitch && (($prevToken['line'] + 1) === $token['line'])) {
                $isFixing = $this->file->addFixableWarning(
                    static::MESSAGE_MISSING_NEWLINE_BETWEEN_TAGS,
                    $tokenPos,
                    static::CODE_MISSING_NEWLINE_BETWEEN_TAGS,
                    [
                        $prevToken['content']
                    ]
                );

                if ($isFixing) {
                    $this->insertNewLine($token);
                }
            }

            $prevToken = $token;
            // Return should be the last element, so this is ok.
            $withReturn = $isReturn;
        }
    }

    /**
     * Checks the alphabetic sorting and registers and error if it sorted wrongly.
     *
     * @throws CodeWarning If the tags are not correctly sorted.
     *
     * @return void
     */
    private function checkAndRegisterSortingError()
    {
        $orgTokens = $this->getTagTokens();
        $sortedTokens = $this->sortTokens($orgTokens);

        if (array_values($orgTokens) !== $sortedTokens) {
            $error = (new CodeWarning(
                static::CODE_WRONG_TAG_SORTING,
                self::MESSAGE_WRONG_TAG_SORTING,
                array_shift($orgTokens)['pointer']
            ))->setToken($this->token);

            $error->isFixable(true);

            throw $error;
        }
    }

    /**
     * Sorts the tags and creates a new doc comment part for them to replace it with the old content.
     *
     * @return string The new content.
     */
    private function createNewSortedTagsContent(): string
    {
        $file = $this->file;
        $eolChar = $file->getEolChar();
        $newContent = '';
        $prevTagContent = '';
        $sortedTags = $this->sortTokens($this->getTagTokens());
        $tagCounts = $this->docTagHelper->getTagCounts($sortedTags);
        $withReturn = false;

        foreach ($sortedTags as $tag) {
            $lineStartingPadding = str_pad('', $tag['column'] - 3, ' ');
            $thisTagContent = $tag['content'];
            $isReturn = $thisTagContent === '@return';

            $isGroupSwitch =
                // Did we switch the tag ...
                $prevTagContent && ($prevTagContent !== $thisTagContent) &&
                // ... but do we skip singles or are we entering the return block which should contain only 1 tag.
                (($tagCounts[$thisTagContent] !== 1) || ($isReturn && !$withReturn));

            // Insert new line between groups.
            if ($isGroupSwitch) {
                $newContent .= $lineStartingPadding . '*' . $eolChar;
            }

            // Create the new Tag.
            // WARNING We do not a line break in the tag summary.
            $newContent .= $lineStartingPadding . '* ' .
                // Remove the "ending" whitespace if there are no more contents
                trim(
                    $thisTagContent . ' ' .
                    implode(' ', array_column($tag['contents'] ?? [], 'content'))
                ) .
                $eolChar;

            $prevTagContent = $thisTagContent;
            $withReturn = $isReturn;
        }

        $newContent .= $lineStartingPadding . '*/' . $eolChar;

        return $newContent;
    }

    /**
     * Sorts the tokens in blocks of their occurences and then alphabetically, but the return at last.
     *
     * @return void
     */
    private function fixSorting()
    {
        $fixer = $this->file->getFixer();

        $fixer->beginChangeset();

        $firstTag = $this->removeOldTagLines();

        $fixer->addContent($firstTag['pointer'] - 1, $this->createNewSortedTagsContent());

        $fixer->endChangeset();
    }

    /**
     * Returns the tokens of the comment tags.
     *
     * @return array The tokens of the comment tags.
     */
    private function getTagTokens(): array
    {
        return $this->docTagHelper->getCommentTagTokens();
    }

    /**
     * Insert the new line before the given token.
     *
     * @param array $token The token where a newline should be.
     *
     * @return void
     */
    private function insertNewLine(array $token)
    {
        $fixer = $this->file->getFixer();
        $lineStartPadding = str_pad('', $token['column'] - 3, ' ');

        $fixer->beginChangeset();

        // Remove the whitespace between the tag and the comments star.
        $fixer->replaceToken($token['pointer'] - 1, '');

        $fixer->addContentBefore(
            $token['pointer'],
            $this->file->getEolChar() . $lineStartPadding . '* '
        );

        $fixer->endChangeset();
    }

    /**
     * Processes a found registered token.
     *
     * @return void
     */
    protected function processToken()
    {
        try {
            $this->checkAndRegisterSortingError();

            $this->checkAndRegisterLineBreakErrors();
        } catch (CodeWarning  $exception) {
            $fixable = $this->getExceptionHandler()->handleException($exception);

            if ($fixable) {
                $this->fixSorting();
            }
        }
    }

    /**
     * Returns an array of registered tokens.
     *
     * @return int[] Returns array of tokens to listen for
     */
    public function register(): array
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /**
     * Removed the lines with the wrongly sorted tags.
     *
     * @return array The first tag token of this doc block.
     */
    private function removeOldTagLines(): array
    {
        $tags = $this->getTagTokens();
        $firstTag = array_shift($tags);

        (new LineHelper($this->file))
            ->removeLines(
                $firstTag['line'],
                $this->tokens[$this->token['comment_closer']]['line']
            );

        return $firstTag;
    }

    /**
     * Do you want to setup things before processing the token?
     *
     * @return void
     */
    protected function setUp()
    {
        $this->addPointerToTokens();

        $this->docTagHelper = new DocTagHelper($this->token, $this->file, $this->stackPos, $this->tokens);
    }

    /**
     * Support for annotations with variable values like symfony annotations which should not influence sorting!
     *
     * @param string $tagName The tag name out of the code.
     *
     * @return string The tag name without "dynamic values".
     */
    private function getRealtagName(string $tagName): string
    {
        $matches = [];

        return (preg_match('/(?P<realTag>@\w+)(?P<separator>$|\(|\\\\|\s)/', $tagName, $matches))
            ? $matches['realTag']
            : $tagName;
    }

    /**
     * Sorts the tokens in blocks of their occurrences and then alphabetically, but the return at last.
     *
     * @param array $tokens The tokens.
     *
     * @return array The sorted tokens.
     */
    private function sortTokens(array $tokens): array
    {
        $tagCounts = $this->docTagHelper->getTagCounts($tokens);

        usort($tokens, function (array $leftToken, array $rightToken) use ($tagCounts): int {
            $return = 0;
            $leftTagName = $leftToken['content'];
            $rightTagName = $rightToken['content'];
            $realLeftTagName = $this->getRealtagName($leftTagName);
            $realRightTagName = $this->getRealtagName($rightTagName);

            if ($realLeftTagName !== $realRightTagName) {
                $leftTagCount = $tagCounts[$leftTagName];
                $rightTagCount = $tagCounts[$rightTagName];

                switch (true) {
                    case ($realLeftTagName === '@return'):
                        $return = 1;
                        break;

                    case ($realRightTagName === '@return'):
                        $return = -1;
                        break;

                    case ($leftTagCount !== $rightTagCount):
                        $return = $leftTagCount > $rightTagCount ? +1 : -1;
                        break;

                    default:
                        $return = strcasecmp($realLeftTagName, $realRightTagName);
                }
            }

            return $return;
        });

        return $tokens;
    }
}
