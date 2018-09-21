<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;

/**
 * Class MultipleReturnSniff.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class MultipleReturnSniff extends AbstractScopeSniff
{
    /**
     * Code for multiple returns.
     *
     * @var string CODE_MULTIPLE_RETURNS_FOUND
     */
    public const CODE_MULTIPLE_RETURNS_FOUND = 'MultipleReturnsFound';

    /**
     * Error message for multiple returns.
     *
     * @var string WARNING_MULTIPLE_RETURNS_FOUND
     */
    private const WARNING_MULTIPLE_RETURNS_FOUND = 'Multiple returns detected. Did you refactor your class?';

    /**
     * MultipleReturnSniff constructor.
     */
    public function __construct()
    {
        parent::__construct([T_FUNCTION], [T_RETURN], false);
    }

    /**
     * Processes the tokens that this test is listening for.
     *
     * @param File $phpcsFile
     * @param int $returnPos
     * @param int $functionPos
     *
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $returnPos, $functionPos): void
    {
        $multipleReturnsFound = $phpcsFile->findPrevious([T_RETURN], $returnPos - 1, $functionPos);

        if ($multipleReturnsFound && $multipleReturnsFound > -1) {
            $phpcsFile->addWarning(
                self::WARNING_MULTIPLE_RETURNS_FOUND,
                $returnPos,
                self::CODE_MULTIPLE_RETURNS_FOUND,
                $phpcsFile->getWarnings()
            );
        }
    }

    /**
     * Processes a token that is found outside the scope that this test is listening to.
     *
     * @param File $phpcsFile The file where this token was found.
     * @param int $stackPtr The position in the stack where this token was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
        // Satisfy PHP MD and do nothing.
        unset($phpcsFile, $stackPtr);
    }
}
