<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\Commenting\TagValidator\TagValidatorFactory;
use BestIt\CodeSniffer\File;
use BestIt\Sniffs\Commenting\AbstractDocSniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;

/**
 * Class DocTagHelper
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Helper
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
     * DocTagHelper constructor.
     *
     * @param File $file The php cs file
     * @param array $commentStartToken The start token of the comment.
     * @param int $stackPtr Pointer to the token which is to be listened
     */
    public function __construct(array $commentStartToken, File $file, int $stackPtr)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();
        $this->stackPtr = $stackPtr;
        $this->commentStartToken = $commentStartToken;
    }

    /**
     * Checks comment tags.
     *
     * @param array $tagMetadata List of tag metadata
     * @param string[] $disallowedTags List of disallowed tags
     *
     * @return void
     */
    public function checkCommentTags(array $tagMetadata, array $disallowedTags)
    {
        $this->tagMetadata = $tagMetadata;
        $this->disallowedTags = $disallowedTags;

        $tagOccurenceHelper = new DocTagOccurenceHelper($this->file, $this->commentStartToken['pointer'], $this);

        $this->validateTags();
        $this->checkAllowedTags();

        $tagOccurenceHelper->checkTagOccurrences($tagMetadata);

        if (count($this->getCommentTagTokens()) === 0) {
            return;
        }
    }

    /**
     * Returns the position of the comment end token.
     *
     * @return int the position in the stack.
     */
    private function getCommentEndPosition(): int
    {
        return $this->commentStartToken['comment_closer'];
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
            $this->getCommentEndPosition() + 1
        );
        $endToken = $this->tokens[$endPtr];

        $tagStringPtr = $this->file->findNext([T_DOC_COMMENT_STRING], $tagPtr, $endPtr);

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
     * Checks if the given tags are allowed.
     *
     * @return void
     */
    private function checkAllowedTags()
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
        $tagPositions = $this->getCommentStartToken()['comment_tags'];
        $tagTokens = [];

        /** @var int $tagPos */
        foreach ($tagPositions as $tagPos) {
            $tagTokens[$tagPos] = $this->tokens[$tagPos] + ['contents' => $this->loadTagContentTokens($tagPos)];
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

            if (!array_key_exists($tagName, $tagCounts)) {
                $tagCounts[$tagName] = 0;
            }

            ++$tagCounts[$tagName];
        }

        return $tagCounts;
    }

    /**
     * Validates the given comment tags.
     *
     * @return void
     */
    private function validateTags()
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

            $validator->validate($tagToken, $stringPtr > 0 ? $this->tokens[$stringPtr] : null);
        }
    }
}
