<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;

/**
 * Class DocTagHelper
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class DocTagHelper
{
    /**
     * The stating token of the comment.
     *
     * @var array
     */
    private $commentStartToken;

    /**
     * The PHP CS file
     *
     * @var File
     */
    private $file;

    /**
     * The token stack from php cs file.
     *
     * @var array
     */
    private $tokens;

    /**
     * Current stack pointer to the token stack of the php cs file.
     *
     * @var int
     */
    private $stackPtr;

    /**
     * DocTagHelper constructor.
     *
     * @param array $commentStartToken The start token of the comment.
     * @param File $file The php cs file
     * @param int $stackPtr Pointer to the token which is to be listened
     * @param array $tokens Another token array if we want to overwrite them,
     */
    public function __construct(array $commentStartToken, File $file, int $stackPtr, array $tokens = [])
    {
        $this->file = $file;
        $this->tokens = $tokens ?: $file->getTokens();
        $this->stackPtr = $stackPtr;
        $this->commentStartToken = $commentStartToken;
    }

    /**
     * Returns the comment start token.
     *
     * @return array the token.
     */
    private function getCommentStartToken(): array
    {
        return $this->commentStartToken;
    }

    /**
     * Loads the tag content for the given tag position.
     *
     * @param int $tagPosition The position of the tag.
     *
     * @return array The content tokens of the tag.
     */
    private function loadTagContentTokens(int $tagPosition): array
    {
        $contents = [];
        $nextOrClosingPos = $this->file->findNext([T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_TAG], $tagPosition + 1);
        $positionsTillEnd = TokenHelper::findNextAll(
            $this->file->getBaseFile(),
            [T_DOC_COMMENT_STRING],
            $tagPosition,
            $nextOrClosingPos
        );

        foreach ($positionsTillEnd as $position) {
            $contents[] = $this->tokens[$position];
        }

        return $contents;
    }

    /**
     * Returns array of all comment tag tokens.
     *
     * @return array List of all comment tag tokens indexed by token pointer
     */
    public function getCommentTagTokens(): array
    {
        $tagPositions = $this->getCommentStartToken()['comment_tags'];
        $tagTokens = [];

        /** @var int $tagPos */
        foreach ($tagPositions as $tagPos) {
            $tagTokens[$tagPos] = $this->tokens[$tagPos] + ['contents' => $this->loadTagContentTokens($tagPos)];
        }

        return $tagTokens;
    }

    /**
     * Returns the individual count of every tag.
     *
     * @param array $tagTokens Array of tag tokens.
     *
     * @return array List of comment tags with there count of the current comment
     */
    public function getTagCounts(array $tagTokens): array
    {
        $tagCounts = [];

        foreach ($tagTokens as $tagToken) {
            $tagName = $tagToken['content'];

            if (!array_key_exists($tagName, $tagCounts)) {
                $tagCounts[$tagName] = 0;
            }

            ++$tagCounts[$tagName];
        }

        return $tagCounts;
    }
}
