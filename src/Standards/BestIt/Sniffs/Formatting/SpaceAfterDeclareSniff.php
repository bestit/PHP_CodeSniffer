<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * Class SpaceAfterDeclareSniff
 *
 * @package BestIt\Sniffs\Formatting
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class SpaceAfterDeclareSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Error message when no whitespace is found.
     *
     * @var string
     */
    const MESSAGE_NO_WHITESPACE_FOUND = 'There is no whitespace after declare-statement.';

    /**
     * Code when no whitespace is found.
     *
     * @var string
     */
    const CODE_NO_WHITESPACE_FOUND = 'NoWhitespaceFound';

    /**
     * Error message when more than one whitespaces are found.
     *
     * @var string
     */
    const MESSAGE_MUCH_WHITESPACE_FOUND = 'There are more than one whitespaces after declare-statement.';

    /**
     * Code when more than one whitespaces are found.
     *
     * @var string
     */
    const CODE_MUCH_WHITESPACE_FOUND = 'MuchWhitespaceFound';

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [
            T_DECLARE
        ];
    }

    /**
     * Called when one of the token types that this sniff is listening for
     * is found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int $stackPtr The position in the PHP_CodeSniffer file's token stack where the token was found.
     *
     * @return void|int Optionally returns a stack pointer.
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $semicolonPtr = $phpcsFile->findEndOfStatement($stackPtr);

        $secondSpacePtr = $semicolonPtr + 2;
        $secondSpaceToken = $tokens[$secondSpacePtr];

        if ($secondSpaceToken['code'] !== T_WHITESPACE) {
            $this->handleNoWhitespaceFound($phpcsFile, $semicolonPtr);

            return;
        }

        $nextNonSpacePtr = $phpcsFile->findNext(T_WHITESPACE, $secondSpacePtr, null, true);

        if ($nextNonSpacePtr === false) {
            return;
        }

        $nextNonSpaceToken = $tokens[$nextNonSpacePtr];

        if (($nextNonSpaceToken['line'] - $secondSpaceToken['line']) > 1) {
            $this->handleMuchWhitespacesFound($phpcsFile, $semicolonPtr, $secondSpacePtr, $nextNonSpacePtr);

            return;
        }
    }

    /**
     * Handles when no whitespace is found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $semicolonPtr
     *
     * @return void
     */
    private function handleNoWhitespaceFound(PHP_CodeSniffer_File $phpcsFile, $semicolonPtr)
    {
        $fixNoWhitespace = $phpcsFile->addFixableError(
            self::MESSAGE_NO_WHITESPACE_FOUND,
            $semicolonPtr,
            self::CODE_NO_WHITESPACE_FOUND
        );

        if ($fixNoWhitespace) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->addNewline($semicolonPtr);
            $phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * Handles when more than one whitespaces are found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $semicolonPtr
     * @param int $secondSpacePtr
     * @param int $nextNonSpacePtr
     *
     * @return void
     */
    private function handleMuchWhitespacesFound(
        PHP_CodeSniffer_File $phpcsFile,
        $semicolonPtr,
        $secondSpacePtr,
        $nextNonSpacePtr
    ) {
        $fixMuchWhitespaces = $phpcsFile->addFixableError(
            self::MESSAGE_MUCH_WHITESPACE_FOUND,
            $semicolonPtr,
            self::CODE_MUCH_WHITESPACE_FOUND
        );

        if ($fixMuchWhitespaces) {
            $phpcsFile->fixer->beginChangeset();
            for ($i = $secondSpacePtr; $i < $nextNonSpacePtr; $i++) {
                $phpcsFile->fixer->replaceToken($i, '');
            }
            $phpcsFile->fixer->endChangeset();
        }
    }
}
