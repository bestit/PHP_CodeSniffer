<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\Helper\DocTagHelper;
use BestIt\CodeSniffer\Helper\LineHelper;
use BestIt\Sniffs\AbstractSniff;
use function array_filter;
use function array_key_exists;
use function array_shift;
use function preg_match;
use function str_pad;
use function str_repeat;
use function strcasecmp;
use function strlen;
use function uasort;
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
    public const CODE_MISSING_NEWLINE_BETWEEN_TAGS = 'MissingNewlineBetweenTags';

    /**
     * You SHOULD sort the tags by their occurrence and then alphabetically, but @return SHOULD be the last.
     */
    public const CODE_WRONG_TAG_SORTING = 'WrongTagSorting';

    /**
     * The message for the missing new line between tags.
     */
    private const MESSAGE_MISSING_NEWLINE_BETWEEN_TAGS = 'There should be a newline after the tag (group): %s.';

    /**
     * The message for the wrong sorting order.
     */
    private const MESSAGE_WRONG_TAG_SORTING = 'Please provide the tags in occurrence and then alphabetical order 
        (a-z) but with return at last position.';

    /**
     * The doc tag helper.
     *
     * @var DocTagHelper
     */
    private DocTagHelper $docTagHelper;

    /**
     * The loaded tokens of this comment.
     *
     * @var array|null
     */
    private ?array $loadedTagTokens = null;

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
    private function checkAndRegisterLineBreakErrors(): void
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
                        $prevToken['content'],
                    ],
                );

                if ($isFixing) {
                    $this->insertNewLine($token, $tokenPos);
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
    private function checkAndRegisterSortingError(): void
    {
        $orgTokens = $this->getTagTokens();
        $sortedTokens = $this->sortTokens($orgTokens);

        if (array_keys(($orgTokens)) !== array_keys($sortedTokens)) {
            reset($orgTokens);
            $firstTokenPos = key($orgTokens);

            $error = (new CodeWarning(
                static::CODE_WRONG_TAG_SORTING,
                self::MESSAGE_WRONG_TAG_SORTING,
                $firstTokenPos,
            ))->setToken($this->token);

            $error->isFixable(true);

            throw $error;
        }
    }

    /**
     * The callback to sort tokens.
     *
     * 1. @return goes to the bottom
     * 2. Single tags are group alphabetically in the top group.
     * 3. Groups are sorted then by occurrence, that the largest group is the last one before the return.
     * 4. Same annotations are kept in the order of their code.
     *
     * @param array $leftToken Provided by usort.
     * @param array $rightToken Provided by usort.
     * @param array $tagCounts Saves the occurence count for every tag.
     *
     * @return int
     */
    private function compareTokensForSorting(array $leftToken, array $rightToken, array $tagCounts): int
    {
        $leftTagName = $leftToken['content'];
        $rightTagName = $rightToken['content'];
        $realLeftTagName = $this->getRealtagName($leftTagName);
        $realRightTagName = $this->getRealtagName($rightTagName);

        // If they have the same content, leave them, where they where ...
        $return = $leftToken['line'] > $rightToken['line'] ? 1 : -1;

        // But if they are different.
        if ($realLeftTagName !== $realRightTagName) {
            $leftTagCount = $tagCounts[$leftTagName];
            $rightTagCount = $tagCounts[$rightTagName];

            switch (true) {
                // Move return to bottom everytime ...
                case ($realLeftTagName === '@return'):
                    $return = 1;
                    break;

                // ... yes, everytime
                case ($realRightTagName === '@return'):
                    $return = -1;
                    break;

                // Move single items to the top.
                case ($leftTagCount !== $rightTagCount):
                    $return = $leftTagCount > $rightTagCount ? +1 : -1;
                    break;

                // Compare tag name
                default:
                    $return = strcasecmp($realLeftTagName, $realRightTagName) > 1 ? 1 : -1;
            }
        }

        return $return;
    }

    /**
     * Sorts the tags and creates a new doc comment part for them to replace it with the old content.
     *
     * @return string The new content.
     */
    private function createNewSortedTagsContent(): string
    {
        $file = $this->file;
        $eolChar = $file->eolChar;
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
            // WARNING We do not need a line break in the tag summary.
            $newContent .= $lineStartingPadding . '* ' . trim($thisTagContent);

            if ($tag['contents']) {
                $prevLine = $tag['line'];
                foreach ($tag['contents'] as $subToken) {
                    // If we have a line switch, we need to create the correct indentation from before ...
                    if ($withLineSwitch = $subToken['line'] > $prevLine) {
                        $newContent .= $eolChar .
                            $lineStartingPadding . '*' .
                            str_repeat(' ', $subToken['column'] - strlen($lineStartingPadding) - 2);

                        $prevLine = $subToken['line'];
                    }

                    // ... if we have no line switch, then an additional whitespace is enough.
                    $newContent .= ($withLineSwitch ? '' : ' ') . $subToken['content'];
                }
            }

            $newContent .= $eolChar;

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
    private function fixSorting(): void
    {
        $fixer = $this->file->fixer;

        $fixer->beginChangeset();

        $firstTagPos = $this->removeOldTagLines();

        $fixer->addContent($firstTagPos - 1, $this->createNewSortedTagsContent());

        $fixer->endChangeset();
    }

    /**
     * Support for annotations with variable values (like the symfony annotations) which should not influence sorting!
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
     * Returns the tokens of the comment tags.
     *
     * @return array The tokens of the comment tags.
     */
    private function getTagTokens(): array
    {
        if ($this->loadedTagTokens === null) {
            $this->loadedTagTokens = $this->loadTagTokens();
        }

        return $this->loadedTagTokens;
    }

    /**
     * Insert the new line before the given token.
     *
     * @param array $token The token where a newline should be.
     * @param int $tokenPos
     *
     * @return void
     */
    private function insertNewLine(array $token, int $tokenPos): void
    {
        $fixer = $this->file->fixer;
        $lineStartPadding = str_pad('', $token['column'] - 3, ' ');

        $fixer->beginChangeset();

        // Remove the whitespace between the tag and the comments star.
        $fixer->replaceToken($tokenPos - 1, '');

        $fixer->addContentBefore(
            $tokenPos,
            $this->file->eolChar . $lineStartPadding . '* ',
        );

        $fixer->endChangeset();
    }

    /**
     * Loads the tokens of this comment.
     *
     * @return array
     */
    private function loadTagTokens(): array
    {
        $barrier = 0;
        $tokens = $this->docTagHelper->getTagTokens();

        $tokens = array_filter($tokens, function (array $token) use (&$barrier): bool {
            $allowed = true;

            if ($barrier) {
                if ($allowed = $token['column'] <= $barrier) {
                    $barrier = 0;
                }
            }

            if ($allowed && array_key_exists('contents', $token)) {
                $barrier = $token['column'];
            }

            return $allowed;
        });

        return $tokens;
    }

    /**
     * Processes a found registered token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        try {
            $this->checkAndRegisterSortingError();

            $this->checkAndRegisterLineBreakErrors();
        } catch (CodeWarning $exception) {
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
     * @return int Positon of the frst removed tag.
     */
    private function removeOldTagLines(): int
    {
        $tags = $this->getTagTokens();

        reset($tags);
        $tokenPos = key($tags);
        $firstTag = array_shift($tags);

        (new LineHelper($this->file))
            ->removeLines(
                $firstTag['line'],
                $this->tokens[$this->token['comment_closer']]['line'],
            );

        return $tokenPos;
    }

    /**
     * Do you want to setup things before processing the token?
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->docTagHelper = new DocTagHelper(
            $this->file,
            $this->stackPos,
            $this->tokens,
        );
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

        uasort($tokens, function (array $leftToken, array $rightToken) use ($tagCounts): int {
            return $this->compareTokensForSorting($leftToken, $rightToken, $tagCounts);
        });

        return $tokens;
    }

    /**
     * Removes the loaded tag tokens.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->loadedTagTokens = null;
    }
}
