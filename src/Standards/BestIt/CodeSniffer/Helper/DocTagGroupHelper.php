<?php

declare(strict_types = 1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;
use BestIt\Sniffs\Commenting\AbstractDocSniff;

/**
 * Class DocTagGroupHelper
 *
 * @package BestIt\CodeSniffer\Helper
 * @author Stephan Weber <stephan.weber@bestit-online.de>
 */
class DocTagGroupHelper
{
    /**
     * List of $commentTagTokens pointers by position
     *
     * @var array
     */
    private $commentTagOrder;

    /**
     * The tag tokens of the comment
     *
     * @var array
     */
    private $commentTagTokens;

    /**
     * The PHP CS file
     *
     * @var File
     */
    private $file;

    /**
     * List of tag metadata
     *
     * @var array
     */
    private $tagMetadata;

    /**
     * The token stack from php cs file.
     *
     * @var array
     */
    private $tokens;

    /**
     * DocTagGroupHelper constructor.
     *
     * @param File $file The php cs file
     * @param array $commentTagTokens The tag tokens of the comment
     * @param array $tagMetadata List of tag metadata
     */
    public function __construct(File $file, array $commentTagTokens, array $tagMetadata)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();
        $this->commentTagTokens = $commentTagTokens;
        $this->tagMetadata = $tagMetadata;
        $this->commentTagOrder = array_keys($commentTagTokens);
    }

    /**
     * Checks if tag groups have a blank line around them.
     *
     * @param array $groups List of tag groups with first and last pointer
     *
     * @return void
     */
    public function checkGroupBlankLines($groups)
    {
        foreach ($groups as $group) {
            $firstGroupTagPos = $this->getTagPositionByPointer($group['firstPointer']);
            $lastGroupTagPos = $this->getTagPositionByPointer($group['lastPointer']);

            $firstGroupTag = $this->commentTagTokens[$group['firstPointer']];
            $lastGroupTag = $this->commentTagTokens[$group['lastPointer']];

            $tagBeforeGroup = [];
            $tagBeforeLineDiff = 0;
            $tagAfterLineDiff = 0;

            if ($firstGroupTagPos > 0) {
                $tagBeforeGroup = $this->getTagByPosition($firstGroupTagPos - 1);
                $tagBeforeLineDiff = $firstGroupTag['line'] - $tagBeforeGroup['line'];
            } elseif ($firstGroupTagPos === 0) {
                $tagBeforeGroup = $this->getSummaryPointer($firstGroupTag['pointer'] - 1);
                $tagBeforeLineDiff = $firstGroupTag['line'] - $tagBeforeGroup['line'];
            }

            if (array_key_exists($lastGroupTagPos + 1, $this->commentTagOrder)) {
                $tagAfterGroup = $this->getTagByPosition($lastGroupTagPos + 1);
                $tagAfterLineDiff = $tagAfterGroup['line'] -  $lastGroupTag['line'];
            }

            if ($tagBeforeLineDiff === 1) {
                $this->addFixableError(
                    $firstGroupTag,
                    $tagBeforeGroup
                );
            }

            if ($tagAfterLineDiff === 1) {
                $this->addFixableError(
                    $lastGroupTag
                );
            }
        }
    }

    /**
     * Finds an returns groups of tags
     *
     * @param array $tagCounts List of comment tags with there count of the current comment
     *
     * @return array List of tag groups with first and last tag pointer
     */
    public function getTagGroups(array $tagCounts): array
    {
        $groups = [];

        foreach ($this->commentTagTokens as $tagPtr => $tagToken) {
            $tagName = $tagToken['content'];
            if ($tagCounts[$tagName] > 1) {
                //Writes first pointer on first iteration
                if (!array_key_exists($tagName, $groups)) {
                    $groups[$tagName]['firstPointer'] = $tagPtr;
                }

                $groups[$tagName]['lastPointer'] = $tagPtr;
            }
        }

        return $groups;
    }

    /**
     * Adds the fixable errors to the file
     *
     * @param array $tag The tag token
     * @param array|null $tagToFix Optional tag token to fix error
     *
     * @return void
     */
    private function addFixableError(array $tag, $tagToFix = null)
    {
        if ($tagToFix === null) {
            $tagToFix = $tag;
        }

        $error = $this->file->addFixableError(
            AbstractDocSniff::MESSAGE_NO_LINE_AROUND_TAG_GROUP,
            $tag['pointer'],
            AbstractDocSniff::CODE_NO_LINES_AROUND_TAG_GROUP,
            [
                $tag['content']
            ]
        );

        $shouldFix = !$this->tagChecksLineAfter($tagToFix);

        if ($error && $shouldFix) {
            $this->fixNoLineAfterTag($tagToFix['pointer']);
        }
    }

    /**
     * Fixes missing line after given tag.
     *
     * @param int $tagPtr Pointer to the current token
     *
     * @return void
     */
    private function fixNoLineAfterTag(int $tagPtr)
    {
        $eolPtr = $this->file->findNext(
            [T_DOC_COMMENT_WHITESPACE],
            $tagPtr + 1,
            null,
            false,
            $this->file->getEolChar()
        );

        $tagToken = $this->tokens[$tagPtr];

        $this->file->getFixer()->beginChangeset();

        $content = $this->file->getEolChar() . str_repeat('    ', $tagToken['level']) . ' *';

        $this->file->getFixer()->addContentBefore($eolPtr, $content);

        $this->file->getFixer()->endChangeset();
    }

    /**
     * Gets the pointer of the previous summary
     *
     * @param int $startLine The line where to start searching
     *
     * @return array The tag token
     */
    private function getSummaryPointer(int $startLine): array
    {
        $commentOpenPointer = $this->file->findPrevious([
            T_DOC_COMMENT_OPEN_TAG
        ], $startLine, null, false);

        $tagBeforePointer = $this->file->findPrevious([
            T_DOC_COMMENT_WHITESPACE,
            T_DOC_COMMENT_STAR

        ], $startLine, $commentOpenPointer, true);

        return $this->tokens[$tagBeforePointer];
    }

    /**
     * Get tag by given position from commentTagOrder
     *
     * @param int $position Index of tag in commentTagOrder
     *
     * @return array The tag token
     */
    private function getTagByPosition(int $position): array
    {
        return $this->commentTagTokens[$this->commentTagOrder[$position]] ?? [];
    }

    /**
     * Returns the tag metadata by given tag name.
     *
     * @param string $tagName Name of the tag
     *
     * @return array Array of tag metadata
     */
    private function getTagMetadata(string $tagName): array
    {
        return $this->tagMetadata[$tagName] ?? [];
    }

    /**
     * Returns the position of the tag pointer in the comment block
     *
     * @param int $pointer The pointer of the tag token
     *
     * @return int Returns the found position or -1
     */
    private function getTagPositionByPointer(int $pointer): int
    {
        $result = array_search($pointer, $this->commentTagOrder, true);

        if ($result >= 0) {
            return $result;
        }

        return -1;
    }

    /**
     * Checks if given tag already implements a line check
     *
     * @param array $tag A tag array
     *
     * @return bool LineAfter is checked by tag
     */
    private function tagChecksLineAfter(array $tag): bool
    {
        $result = $tag['code'] === T_DOC_COMMENT_STRING;

        if ($tag['code'] === T_DOC_COMMENT_TAG) {
            $tagMetadata = $this->getTagMetadata($tag['content']);
            $result = array_key_exists('lineAfter', $tagMetadata) && $tagMetadata['lineAfter'];
        }

        return $result;
    }
}
