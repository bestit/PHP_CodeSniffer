<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\Sniffs\Commenting\AbstractDocSniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Class DocHelper
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class DocHelper
{
    /**
     * The php cs file.
     *
     * @var File
     */
    private $file;

    /**
     * Pointer to the token which is to be listened.
     *
     * @var int
     */
    private $stackPtr;

    /**
     * Token stack of the current file.
     *
     * @var array
     */
    private $tokens;

    /**
     * DocHelper constructor.
     *
     * @param File $file File object of file which is processed.
     * @param int $stackPtr Pointer to the token which is processed.
     */
    public function __construct(File $file, $stackPtr)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();
        $this->stackPtr = $stackPtr;
    }

    /**
     * Checks if a comment for the class exists.
     *
     * @param int $listenerPtr Pointer of the listener token
     * @param bool $isVariable Is the current token a variable
     *
     * @return bool Indicator if the comment exists or not
     */
    public function checkCommentExists(int $listenerPtr, bool $isVariable): bool
    {
        $listenerToken = $this->tokens[$listenerPtr];
        $commentEndToken = $this->getCommentEndToken();
        $commentExists = true;

        if ($commentEndToken['type'] !== 'T_DOC_COMMENT_CLOSE_TAG'
            || ($listenerToken['line'] - 1) !== $commentEndToken['line']
        ) {
            $commentExists = false;
        }

        if (!$isVariable && !$commentExists) {
            $this->file->addError(
                AbstractDocSniff::MESSAGE_NO_IMMEDIATE_DOC_FOUND,
                $listenerPtr,
                AbstractDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND
            );
        }

        return $commentExists;
    }

    /**
     * Checks if the comment is multi line.
     *
     * @param bool $isVariable Is the current token a variable
     *
     * @return bool Indicator if the comment is multiline
     */
    public function checkCommentMultiLine(bool $isVariable): bool
    {
        $commentStart = $this->getCommentStartToken();
        $commentEnd = $this->getCommentEndToken();

        if (!$isVariable && $commentStart['line'] === $commentEnd['line']) {
            $this->file->addErrorOnLine(
                AbstractDocSniff::MESSAGE_COMMENT_NOT_MULTI_LINE,
                $commentStart['line'],
                AbstractDocSniff::CODE_COMMENT_NOT_MULTI_LINE
            );

            return false;
        }

        return true;
    }

    /**
     * Returns pointer to the class comment end.
     *
     * @return int Pointer to the class comment end.
     */
    public function getCommentEndPointer(): int
    {
        $whitelistedTokens = array_merge(
            [T_WHITESPACE],
            Tokens::$methodPrefixes
        );

        return $this->file->findPrevious(
            $whitelistedTokens,
            $this->stackPtr - 1,
            null,
            true
        );
    }

    /**
     * Returns token data of the evaluated class comment end.
     *
     * @return array Token data of the comment end.
     */
    public function getCommentEndToken(): array
    {
        return $this->tokens[$this->getCommentEndPointer()];
    }

    /**
     * Returns pointer to the class comment start.
     *
     * @return int Pointer to the class comment start.
     */
    public function getCommentStartPointer(): int
    {
        $commentEndToken = $this->getCommentEndToken();

        return $commentEndToken['comment_opener'];
    }

    /**
     * Returns token data of the evaluated class comment start.
     *
     * @return array Token data of the comment start.
     */
    public function getCommentStartToken(): array
    {
        $commentStartPtr = $this->getCommentStartPointer();

        return $this->tokens[$commentStartPtr];
    }
}
