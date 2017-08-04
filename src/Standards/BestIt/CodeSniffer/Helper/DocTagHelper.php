<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;
use BestIt\Sniffs\Commenting\AbstractDocSniff;
use BestIt\CodeSniffer\Commenting\TagValidator\TagValidatorFactory;

/**
 * Class DocTagHelper
 *
 * @package BestIt\Helper
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class DocTagHelper
{
    /**
     * The PHP CS file
     *
     * @var File
     */
    private $file;

    /**
     * The doc comment helper.
     *
     * @var DocHelper
     */
    private $docHelper;

    /**
     * The token stack from php cs file.
     *
     * @var array
     */
    private $tokens;

    /**
     * Array of tag metadata.
     *
     * @var array
     */
    private $tagMetadata;

    /**
     * Array of disallowed tags.
     *
     * @var string[]
     */
    private $disallowedTags;

    /**
     * Current stack pointer to the token stack of the php cs file.
     *
     * @var int
     */
    private $stackPtr;

    /**
     * DocSummaryHelper constructor.
     *
     * @param File $file The php cs file
     * @param DocHelper $docHelper The doc comment helper
     * @param int $stackPtr Pointer to the token which is to be listened
     */
    public function __construct(File $file, DocHelper $docHelper, int $stackPtr)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();
        $this->docHelper = $docHelper;
        $this->stackPtr = $stackPtr;
    }

    /**
     * Checks comment tags.
     *
     * @param array $tagMetadata List of tag metadata
     * @param string[] $disallowedTags List of disallowed tags
     *
     * @return void
     */
    public function checkCommentTags(array $tagMetadata, array $disallowedTags): void
    {
        $this->tagMetadata = $tagMetadata;
        $this->disallowedTags = $disallowedTags;

        $tagOccurenceHelper = new DocTagOccurenceHelper($this->file, $this->docHelper, $this);
        $tagSortingHelper = new DocTagSortingHelper($this->file, $this);

        $this->validateTags();
        $this->checkAllowedTags();
        $tagOccurenceHelper->checkTagOccurrences($tagMetadata);

        if (count($this->getCommentTagTokens()) === 0
            || !$tagSortingHelper->checkTagSorting($tagMetadata)
        ) {
            return;
        }

        $this->checkBlankLinesAfterTags();
    }

    /**
     * Checks if there are blank lines after tags.
     *
     * @return void
     */
    private function checkBlankLinesAfterTags(): void
    {
        $tagTokens = $this->getCommentTagTokens();

        $lastTagPtr = null;
        $lastTagToken = null;

        foreach ($tagTokens as $tagPtr => $tagToken) {
            if ($lastTagPtr !== null) {
                $this->checkBlankLinesAfterTag($lastTagPtr, $lastTagToken, $tagToken);
            }

            $lastTagPtr = $tagPtr;
            $lastTagToken = $tagToken;
        }

        $this->checkNoBlankLineAfterLastTag($lastTagToken, $lastTagPtr);
    }

    /**
     * Checks if there are blank lines after given last tag and current tag.
     *
     * @param int $lastTagPtr Pointer to the previous tag
     * @param array $lastTagToken Token data of the previous tag
     * @param array $tagToken Token data of the current token
     *
     * @return void
     */
    private function checkBlankLinesAfterTag(int $lastTagPtr, array $lastTagToken, array $tagToken): void
    {
        $lastTagName = $lastTagToken['content'];
        $lastTagEndPtr = $this->getTagEndPointer($lastTagPtr);

        $lastTagEndToken = $this->tokens[$lastTagEndPtr];

        $lineDiff = $tagToken['line'] - $lastTagEndToken['line'] - 1;

        // Detect tag group
        if ($lastTagName === $tagToken['content']) {
            $this->checkBlankLinesInTagGroup($lastTagPtr, $tagToken, $lineDiff, $lastTagEndToken);

            return;
        }

        $this->checkLinesAfterTag($lastTagToken, $tagToken, $lineDiff, $lastTagEndToken);
    }

    /**
     * Detects the end of a tag especially for tags with multiline descriptions.
     *
     * @param int $tagPtr Pointer to the tag
     *
     * @return int Pointer to the end of the given tag
     */
    private function getTagEndPointer(int $tagPtr): int
    {
        $tagToken = $this->tokens[$tagPtr];

        $endPtr = $this->file->findNext(
            [
                T_DOC_COMMENT_TAG,
                T_DOC_COMMENT_CLOSE_TAG
            ],
            $tagPtr + 1,
            $this->docHelper->getCommentEndPointer() + 1
        );
        $endToken = $this->tokens[$endPtr];

        $tagStringPtr = $this->file->findNext([T_DOC_COMMENT_STRING], $tagPtr, $endPtr - 1);

        // If no comment string is found between current tag and upcoming ending return tagPtr
        if ($tagStringPtr === -1) {
            return $tagPtr;
        }

        //Tag not multiline
        if (($endToken['line'] - $tagToken['line'] - 1) === 0) {
            return $tagStringPtr;
        }

        $stringPtr = null;

        foreach ($this->tokens as $tokenPtr => $token) {
            if ($token['line'] <= $tagToken['line']) {
                continue;
            }

            if ($token['code'] === T_DOC_COMMENT_STRING) {
                $stringPtr = $tokenPtr;
            }

            if ($token['line'] >= $endToken['line']) {
                break;
            }
        }

        return $stringPtr ?? $tagStringPtr;
    }

    /**
     * Fixes too much lines after tag.
     *
     * @param array $lastTagToken Token data of previous tag
     * @param array $tagToken Token data of current tag
     *
     * @return void
     */
    private function fixMuchLinesAfterTag(array $lastTagToken, array $tagToken): void
    {
        $this->file->getFixer()->beginChangeset();

        $this->file->getFixer()->removeLines($lastTagToken['line'] + 1, $tagToken['line'] - 1);

        $this->file->getFixer()->endChangeset();
    }

    /**
     * Fixes that there is no line after given tag.
     *
     * @param int $tagPtr Pointer to the current token
     *
     * @return void
     */
    private function fixNoLineAfterTag(int $tagPtr): void
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
     * Checks if the given tags are allowed.
     *
     * @return void
     */
    private function checkAllowedTags(): void
    {
        $tagTokens = $this->getCommentTagTokens();

        foreach ($tagTokens as $tagPtr => $tagToken) {
            if (!in_array(strtolower($tagToken['content']), $this->disallowedTags, true)) {
                continue;
            }

            $this->file->addError(
                AbstractDocSniff::MESSAGE_TAG_NOT_ALLOWED,
                $tagPtr,
                AbstractDocSniff::CODE_TAG_NOT_ALLOWED,
                [
                    $tagToken['content']
                ]
            );
        }
    }

    /**
     * Returns array of all comment tag tokens.
     *
     * @return array List of all comment tag tokens indexed by token pointer
     */
    public function getCommentTagTokens(): array
    {
        $commentStartToken = $this->docHelper->getCommentStartToken();

        /**
         * @var int[] $tagPtrs
         */
        $tagPtrs = $commentStartToken['comment_tags'];
        $tagTokens = [];

        foreach ($tagPtrs as $tagPtr) {
            $tagTokens[$tagPtr] = $this->tokens[$tagPtr];
        }

        return $tagTokens;
    }

    /**
     * Checks if the processed file has a namespace.
     *
     * @return bool Indicator if the file has a namespace or not
     */
    public function hasNamespace(): bool
    {
        $namespacePtr = $this->file->findNext(
            [T_NAMESPACE],
            0
        );

        return $namespacePtr !== -1;
    }

    /**
     * Checks if the listener function is a php function.
     *
     * @return bool Indicator if the current function is not a whitelisted function
     */
    public function isNoWhitelistedFunction(): bool
    {
        $whitelist = [
            '__construct',
            '__destruct',
            '__clone',
            '__wakeup',
            '__set',
            '__unset',
        ];

        $stackToken = $this->tokens[$this->stackPtr];

        $functionNamePtr = $this->file->findNext(
            [T_STRING],
            $this->stackPtr + 1,
            $stackToken['parenthesis_opener']
        );

        $functionNameToken = $this->tokens[$functionNamePtr];

        return !in_array($functionNameToken['content'], $whitelist, true);
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

            if (!isset($tagCounts[$tagName])) {
                $tagCounts[$tagName] = 0;
            }

            $tagCounts[$tagName]++;
        }

        return $tagCounts;
    }

    /**
     * Checks the blank lines after the last tag.
     *
     * @param array $lastTagToken Token data of the previous tag
     * @param int $lastTagPtr Pointer to the previous tag
     *
     * @return void
     */
    private function checkNoBlankLineAfterLastTag(array $lastTagToken, int $lastTagPtr): void
    {
        $commentEndToken = $this->docHelper->getCommentEndToken();

        $lastTagEndPtr = $this->getTagEndPointer($lastTagPtr);
        $lastTagEndToken = $this->tokens[$lastTagEndPtr];

        $lineDiff = $commentEndToken['line'] - $lastTagEndToken['line'] - 1;

        if ($lineDiff > 0) {
            $fixMuchLines = $this->file->addFixableError(
                AbstractDocSniff::MESSAGE_MUCH_LINES_AFTER_TAG,
                $lastTagPtr,
                AbstractDocSniff::CODE_MUCH_LINES_AFTER_TAG,
                [
                    $lastTagToken['content']
                ]
            );

            if ($fixMuchLines) {
                $this->fixMuchLinesAfterTag($lastTagEndToken, $commentEndToken);
            }
        }
    }

    /**
     * Adds error when there are too much lines after an tag.
     *
     * @param int $lastTagPtr Pointer to the previous token
     * @param array $lastTagToken Token data of the previous token
     * @param array $tagToken Token data of the current token
     *
     * @return void
     */
    private function addMuchLinesAfterTagError(int $lastTagPtr, array $lastTagToken, array $tagToken): void
    {
        $lastTagName = $lastTagToken['content'];

        $fixMuchLines = $this->file->addFixableError(
            AbstractDocSniff::MESSAGE_MUCH_LINES_AFTER_TAG,
            $lastTagPtr,
            AbstractDocSniff::CODE_MUCH_LINES_AFTER_TAG,
            [
                $lastTagName
            ]
        );

        if ($fixMuchLines) {
            $this->fixMuchLinesAfterTag($lastTagToken, $tagToken);
        }
    }

    /**
     * Validates the given comment tags.
     *
     * @return void
     */
    private function validateTags(): void
    {
        $tagTokens = $this->getCommentTagTokens();

        $tagValidatorFactory = new TagValidatorFactory();

        foreach ($tagTokens as $tagPtr => $tagToken) {
            $validator = $tagValidatorFactory->createFromTagName($this->file, $tagToken['content']);

            if ($validator === null) {
                continue;
            }

            $tagEndPtr = $this->getTagEndPointer($tagPtr);
            $stringPtr = $this->file->findNext([T_DOC_COMMENT_STRING], $tagPtr + 1, $tagEndPtr + 1);


            $validator->validate($tagToken, $stringPtr, $stringPtr > 0 ? $this->tokens[$stringPtr] : null);
        }
    }

    /**
     * Adds no line after tag error.
     *
     * @param int $lastTagPtr Pointer to the previous tag token
     * @param string $lastTagName Name of the tag
     *
     * @return void
     */
    private function addNoLineAfterTagError(int $lastTagPtr, string $lastTagName): void
    {
        $fixNoLine = $this->file->addFixableError(
            AbstractDocSniff::MESSAGE_NO_LINE_AFTER_TAG,
            $lastTagPtr,
            AbstractDocSniff::CODE_NO_LINE_AFTER_TAG,
            [
                $lastTagName
            ]
        );

        if ($fixNoLine) {
            $this->fixNoLineAfterTag($lastTagPtr);
        }
    }

    /**
     * Checks blank lines in tag groups
     *
     * @param int $lastTagPtr Pointer to the previous tag
     * @param array $tagToken Token data of tag token
     * @param int $lineDiff Difference in lines between two tags in a group
     * @param array $lastTagEndToken End token of a tag
     *
     * @return void
     */
    private function checkBlankLinesInTagGroup(
        int $lastTagPtr,
        array $tagToken,
        int $lineDiff,
        array $lastTagEndToken
    ): void {
        // Check if spacing between two tags in a group is more than 0
        if ($lineDiff > 0) {
            $this->addMuchLinesAfterTagError($lastTagPtr, $lastTagEndToken, $tagToken);
        }
    }

    /**
     * Checks for no line or too many lines after tag.
     *
     * @param array $lastTagToken Token data of the last tag
     * @param array $tagToken Current tag token data
     * @param int $lineDiff Difference between last tag and current tag
     * @param array $lastTagEndToken Token data of the end tag token
     *
     * @return void
     */
    private function checkLinesAfterTag(
        array $lastTagToken,
        array $tagToken,
        int $lineDiff,
        array $lastTagEndToken
    ): void {
        $lastTagName = $lastTagToken['content'];
        $tagMetadata = $this->getTagMetadata($lastTagName);

        $needLineAfter = isset($tagMetadata['lineAfter']) && $tagMetadata['lineAfter'] === true;

        // Skip only if line after tag is needed and line is already there
        // if not continue and look if there are too much lines
        if ($needLineAfter && $lineDiff === 1) {
            return;
        }

        // Force line only when explicitly set
        if ($needLineAfter && $lineDiff === 0) {
            $this->addNoLineAfterTagError($lastTagToken['pointer'], $lastTagName);

            return;
        }

        if ($lineDiff > 1) {
            $this->addMuchLinesAfterTagError($lastTagToken['pointer'], $lastTagEndToken, $tagToken);
        }
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
}
