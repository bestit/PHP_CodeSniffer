<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Class SpaceAfterDeclareSniff
 *
 * @package BestIt\Sniffs\Formatting
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class SpaceAfterDeclareSniff implements Sniff
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
     * @return int[] List of tokens to listen for
     */
    public function register(): array
    {
        return [
            T_DECLARE
        ];
    }

    /**
     * Called when one of the token types that this sniff is listening for is found.
     *
     * @param File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int $stackPtr The position in the PHP_CodeSniffer file's token stack where the token was found.
     *
     * @return void Optionally returns a stack pointer.
     */
    public function process(File $phpcsFile, $stackPtr)
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
     * @param File $phpcsFile The php cs file
     * @param int $semicolonPtr Pointer to the semicolon token
     *
     * @return void
     */
    private function handleNoWhitespaceFound(File $phpcsFile, int $semicolonPtr)
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
     * @param File $phpcsFile The php cs file
     * @param int $semicolonPtr Pointer to the semicolon
     * @param int $secondSpacePtr Pointer to the second space
     * @param int $nextNonSpacePtr Pointer to the next non space token
     *
     * @return void
     */
    private function handleMuchWhitespacesFound(
        File $phpcsFile,
        int $semicolonPtr,
        int $secondSpacePtr,
        int $nextNonSpacePtr
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
