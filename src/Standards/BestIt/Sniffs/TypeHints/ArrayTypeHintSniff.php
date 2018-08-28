<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Sniff for warning at generic array type hints
 *
 * @package BestIt\Sniffs\TypeHints
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 */
class ArrayTypeHintSniff implements Sniff
{
    /**
     * Error message when type hint is generic array.
     *
     * @var string
     */
    const ERROR_GENERIC_ARRAY = 'Use explicit types instead of generic "array". (eg. string[], int[], ...)';

    /**
     * Code message when type hint is generic array.
     *
     * @var string
     */
    const CODE_GENERIC_ARRAY = 'ExplicitTypeInsteadGenericArray';

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[] List of tokens to listen for
     */
    public function register(): array
    {
        return [
            T_DOC_COMMENT_TAG
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

        if (!in_array($tokens[$stackPtr]['content'], ['@return', '@param'], true)) {
            return;
        }

        $stackPtr++;
        while ($tokens[$stackPtr]['type'] === 'T_DOC_COMMENT_WHITESPACE') {
            $stackPtr++;
        }

        $token = $tokens[$stackPtr];

        if (preg_match('/^\s*([a-z|]+)*array/i', $token['content'], $matches) === 1) {
           $phpcsFile->addWarning(
                self::ERROR_GENERIC_ARRAY,
                $stackPtr,
                self::CODE_GENERIC_ARRAY
            );
        }
    }
}
