<?php

namespace BestIt\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;

/**
 * Class MultipleReturnSniff.
 *
 * @package BestIt\Sniffs\Functions
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
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
    public const WARNING_MULTIPLE_RETURNS_FOUND = 'Multiple returns detected. Did you refactor your class?';

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
     * @param $functionPos
     * @param $classPos
     *
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $functionPos, $classPos): void
    {
        $multipleReturnsFound = $phpcsFile->findPrevious([T_RETURN], $functionPos - 1, $classPos);

        if ($multipleReturnsFound && $multipleReturnsFound > -1) {
            self::throwWarning($phpcsFile, $functionPos);
        }
    }

    /**
     * Throws a warning.
     *
     * @param File $phpcsFile The file to test.
     * @param int $stackPtr Position where to throw the warning.
     *
     * @return void
     */
    private static function throwWarning(File $phpcsFile, $stackPtr): void
    {
        $phpcsFile->addError(
            self::WARNING_MULTIPLE_RETURNS_FOUND,
            $stackPtr,
            self::CODE_MULTIPLE_RETURNS_FOUND,
            $phpcsFile->getWarnings()
        );
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
    }
}
