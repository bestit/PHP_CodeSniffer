<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Class OpenTagSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class OpenTagSniff implements Sniff
{
    /**
     * Error message when open tag is not first statement.
     */
    public const ERROR_NOT_FIRST_STATEMENT = 'Open tag is not first statement';

    /**
     * Code when open tag is not first statement.
     */
    public const CODE_NOT_FIRST_STATEMENT = 'OpenTagNotFirstStatement';

    /**
     * Error message when there is no space after open tag.
     */
    public const ERROR_NO_SPACE_AFTER_OPEN_TAG = 'No space after open tag';

    /**
     * Code when there is no space after open tag.
     */
    public const CODE_NO_SPACE_AFTER_OPEN_TAG = 'NoSpaceAfterOpenTag';

    /**
     * Error message when line after open tag is not empty.
     */
    public const ERROR_LINE_NOT_EMPTY = 'Line after open tag is not empty.';

    /**
     * Code when line after open tag is not empty.
     */
    public const CODE_LINE_NOT_EMPTY = 'LineNotEmpty';

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[] List of tokens to listen for
     */
    public function register(): array
    {
        return [
            T_OPEN_TAG
        ];
    }

    /**
     * Called when one of the token types that this sniff is listening for is found.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int $stackPtr The position in the PHP_CodeSniffer file's token stack where the token was found.
     *
     * @return void Optionally returns a stack pointer.
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $tokens = $phpcsFile->getTokens();

        //Open tag is not first token in token stack
        if ($stackPtr !== 0) {
            $this->handleOpenTagNotFirstStatement($phpcsFile, $stackPtr);
        }

        $whitespacePtr = $stackPtr + 1;
        $whitespaceToken = $tokens[$whitespacePtr];

        //Following token not whitespace token
        if ($whitespaceToken['code'] !== T_WHITESPACE) {
            $this->handleNoSpaceAfterOpenTag($phpcsFile, $stackPtr, $whitespacePtr);

            return;
        }

        //Whitespace token not empty
        if ($whitespaceToken['length'] !== 0) {
            $this->handleLineNotEmpty($phpcsFile, $whitespacePtr);
        }
    }

    /**
     * Handles open tag not first statement error.
     *
     * @param File $phpcsFile The php cs file
     * @param int $stackPtr Pointer to the open tag token
     *
     * @return void
     */
    private function handleOpenTagNotFirstStatement(File $phpcsFile, int $stackPtr): void
    {
        $fixNotFirstStatement = $phpcsFile->addFixableError(
            self::ERROR_NOT_FIRST_STATEMENT,
            $stackPtr,
            self::CODE_NOT_FIRST_STATEMENT
        );

        if ($fixNotFirstStatement) {
            $phpcsFile->fixer->beginChangeset();
            for ($i = 0; $i < $stackPtr; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * Handles no space after open tag error.
     *
     * @param File $phpcsFile The php cs file
     * @param int $stackPtr Pointer to the open tag token
     * @param int $whitespacePtr Pointer to the line after the open tag
     *
     * @return void
     */
    private function handleNoSpaceAfterOpenTag(File $phpcsFile, int $stackPtr, int $whitespacePtr): void
    {
        $fixNoSpaceAfterTag = $phpcsFile->addFixableError(
            self::ERROR_NO_SPACE_AFTER_OPEN_TAG,
            $whitespacePtr,
            self::CODE_NO_SPACE_AFTER_OPEN_TAG
        );

        if ($fixNoSpaceAfterTag) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->addNewline($stackPtr);
            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * Handles line after open tag not empty.
     *
     * @param File $phpcsFile The php cs file
     * @param int $whitespacePtr Pointer to the line after the open tag
     *
     * @return void
     */
    private function handleLineNotEmpty(File $phpcsFile, int $whitespacePtr): void
    {
        $fixSpaceNotScndLine = $phpcsFile->addFixableError(
            self::ERROR_LINE_NOT_EMPTY,
            $whitespacePtr,
            self::CODE_LINE_NOT_EMPTY
        );

        if ($fixSpaceNotScndLine) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken(
                $whitespacePtr,
                ''
            );
            $phpcsFile->fixer->endChangeset();
        }
    }
}
