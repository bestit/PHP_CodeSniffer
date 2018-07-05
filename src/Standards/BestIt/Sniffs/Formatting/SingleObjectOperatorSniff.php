<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Only allow a single object operator per line
 *
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class SingleObjectOperatorSniff implements Sniff
{
    /**
     * Error message when found more then one object operator per line.
     *
     * @var string
     */
    const ERROR_NOT_SINGLE_OBJECT_OPERATOR_STATEMENT = 'Only one object operator per line allowed';

    /**
     * Code when found more then one object operator per line.
     *
     * @var string
     */
    const CODE_NOT_SINGLE_OBJECT_OPERATOR_STATEMENT = 'NotSingleObjectOperatorStatement';

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[] List of tokens to listen for
     */
    public function register(): array
    {
        return [
            T_OBJECT_OPERATOR
        ];
    }

    /**
     * Called when one of the token types that this sniff is listening for is found.
     *
     * @param File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int $stackPtr The position in the PHP_CodeSniffer file's token stack where the token was found.
     *
     * @return void|int Optionally returns a stack pointer.
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($this->isValidOperator($tokens, $stackPtr) === false) {
            $statement = $phpcsFile->addError(
                self::ERROR_NOT_SINGLE_OBJECT_OPERATOR_STATEMENT,
                $stackPtr,
                self::CODE_NOT_SINGLE_OBJECT_OPERATOR_STATEMENT
            );

            if ($statement) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewlineBefore($stackPtr);
                $phpcsFile->fixer->endChangeset();
            }
        }
    }

    /**
     * Check if given position of object operator is valid
     *
     * @param array $tokens
     * @param int $stackPtr
     *
     * @return bool
     */
    private function isValidOperator($tokens, $stackPtr)
    {
        // First operator for variable
        if ($tokens[$stackPtr - 1]['code'] === T_VARIABLE) {
            return true;
        }

        // First operator of line
        $offset = $stackPtr;
        do {
            --$offset;

            $prevToken = $tokens[$offset] ?? null;

            if ($prevToken['code'] !== T_WHITESPACE) {
                break;
            }

            if ($prevToken['length'] === 0) {
                return true;
            }
        } while ($prevToken !== null);

        return false;
    }
}
