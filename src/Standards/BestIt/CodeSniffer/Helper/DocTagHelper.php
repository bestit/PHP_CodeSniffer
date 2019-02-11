<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;
use function array_key_exists;
use function in_array;
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
    private $token;

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
     * DocTagHelper constructor.
     *
     * @param File $file The php cs file
     * @param int $stackPos Position to the token which is to be listened
     * @param array $tokens Another token array if we want to overwrite them.
     */
    public function __construct(File $file, int $stackPos, array $tokens = [])
    {
        $this->file = $file;
        $this->tokens = $tokens ?: $file->getTokens();
        $this->token = $this->tokens[$stackPos];
    }

    /**
     * Returns the comment start token.
     *
     * @return array the token.
     */
    private function getCommentStartToken(): array
    {
        return $this->token;
    }

    /**
     * Loads the tag content for the given tag position.
     *
     * @param int $tagPosition The position of the tag.
     * @param int $iteratedPosition
     *
     * @return array The content tokens of the tag.
     */
    private function loadTagContentTokens(int $tagPosition, int &$iteratedPosition): array
    {
        $contents = [];
        $myColumn = $this->tokens[$tagPosition]['column'];
        $closingPos = $this->file->findNext([T_DOC_COMMENT_CLOSE_TAG], $position = $tagPosition + 1);

        while ($position < $closingPos) {
            $contentToken = $this->tokens[$position++];

            if (($contentToken['code'] === T_DOC_COMMENT_TAG) && ($contentToken['column'] <= $myColumn)) {
                break;
            }

            if (in_array($contentToken['code'], [T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG])) {
                $contents[$position] = $contentToken;
            }

            $iteratedPosition = $position;
        }

        return $contents;
    }

    /**
     * Returns array of all comment tag tokens.
     *
     * @return array List of all comment tag tokens indexed by token position
     */
    public function getTagTokens(): array
    {
        $iteratedPos = 0;
        $tagPositions = $this->getCommentStartToken()['comment_tags'];
        $tagTokens = [];

        /** @var int $tagPos */
        foreach ($tagPositions as $tagPos) {
            if ($tagPos >= $iteratedPos) {
                $tagTokens[$tagPos] = $this->tokens[$tagPos] + [
                    'contents' => $this->loadTagContentTokens($tagPos, $iteratedPos)
                ];
            }
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
