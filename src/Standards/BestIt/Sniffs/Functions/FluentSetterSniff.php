<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Standards_AbstractScopeSniff;

/**
 * Class FluentSetterSniff
 *
 * @package BestIt\Sniffs\Functions
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class FluentSetterSniff extends PHP_CodeSniffer_Standards_AbstractScopeSniff
{
    /**
     * Code when multiple return statements are found.
     *
     * @var string
     */
    const CODE_MULTIPLE_RETURN_FOUND = 'MultipleReturnFound';

    /**
     * Code when the method does not return $this.
     *
     * @var string
     */
    const CODE_MUST_RETURN_THIS = 'MustReturnThis';

    /**
     * Code when no return statement is found.
     *
     * @var string
     */
    const CODE_NO_RETURN_FOUND = 'NoReturnFound';

    /**
     * Error message when no return statement is found.
     *
     * @var string
     */
    const ERROR_NO_RETURN_FOUND = 'Method "%s" has no return statement';

    /**
     * Error message when multiple return statements are found.
     *
     * @var string
     */
    const ERROR_MULTIPLE_RETURN_FOUND = 'Method "%s" has multiple return statements';

    /**
     * Error message when the method does not return $this.
     *
     * @var string
     */
    const ERROR_MUST_RETURN_THIS = 'The method "%s" must return $this';

    /**
     * Specifies how an identation looks like.
     *
     * @var string
     */
    public $identation = '    ';

    /**
     * FluentSetterSniff constructor.
     */
    public function __construct()
    {
        parent::__construct([T_CLASS, T_ANON_CLASS, T_TRAIT], [T_FUNCTION], false);
    }

    /**
     * Processes the tokens that this test is listening for.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file where this token was found.
     * @param int $stackPtr The position in the stack where this token was found.
     * @param int $currScope The position in the tokens array that opened the scope that this test is listening for.
     *
     * @return void
     */
    protected function processTokenWithinScope(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $currScope
    ) {
        $className = $phpcsFile->getDeclarationName($currScope);
        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        if (!$this->checkIfSetterFunction($methodName)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $errorData = sprintf('%s::%s', $className, $methodName);

        $functionToken = $tokens[$stackPtr];
        $openBracePtr = $functionToken['scope_opener'];
        $closeBracePtr = $functionToken['scope_closer'];

        $returnPtr = $phpcsFile->findNext(T_RETURN, $openBracePtr, $closeBracePtr);

        if ($returnPtr === false) {
            $fixNoReturnFound = $phpcsFile->addFixableError(
                self::ERROR_NO_RETURN_FOUND,
                $stackPtr,
                self::CODE_NO_RETURN_FOUND,
                $errorData
            );

            if ($fixNoReturnFound) {
                $this->fixNoReturnFound($phpcsFile, $closeBracePtr);
            }

            return;
        }

        $nextReturnPtr = $phpcsFile->findNext(
            T_RETURN,
            $returnPtr + 1,
            $closeBracePtr
        );

        if ($nextReturnPtr !== false) {
            $phpcsFile->addError(
                self::ERROR_MULTIPLE_RETURN_FOUND,
                $stackPtr,
                self::CODE_MULTIPLE_RETURN_FOUND,
                $errorData
            );
            return;
        }

        $thisVariablePtr = $phpcsFile->findNext(T_VARIABLE, $returnPtr, null, false, '$this', true);

        if ($thisVariablePtr === false) {
            $fixMustReturnThis = $phpcsFile->addFixableError(
                self::ERROR_MUST_RETURN_THIS,
                $stackPtr,
                self::CODE_MUST_RETURN_THIS,
                $errorData
            );

            if ($fixMustReturnThis) {
                $this->fixMustReturnThis($phpcsFile, $returnPtr);
            }

            return;
        }
    }

    /**
     * Checks if the given method name relates to a setter function.
     *
     * Its not a simple strpos because a method name like "setupDatabase" would be catched.
     * We check that the letters until the first upper case character equals "set".
     * This way we expect that after "set" follows an upper case letter.
     *
     * @param string $methodName Current method name
     *
     * @return bool Indicator if the given method is a setter function
     */
    private function checkIfSetterFunction(string $methodName): bool
    {
        $firstMatch = strcspn($methodName, 'ABCDEFGHJIJKLMNOPQRSTUVWXYZ');

        return substr($methodName, 0, $firstMatch) === 'set';
    }

    /**
     * Fixes if no return statement is found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The php cs file
     * @param int $closingBracePtr Pointer to the closing curly brace of the function
     *
     * @return void
     */
    private function fixNoReturnFound(PHP_CodeSniffer_File $phpcsFile, int $closingBracePtr)
    {
        $tokens = $phpcsFile->getTokens();
        $closingBraceToken = $tokens[$closingBracePtr];

        $expectedReturnSpaces = str_repeat($this->identation, $closingBraceToken['level'] + 1);

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->addNewlineBefore($closingBracePtr - 1);
        $phpcsFile->fixer->addContentBefore($closingBracePtr - 1, $expectedReturnSpaces . 'return $this;');
        $phpcsFile->fixer->addNewlineBefore($closingBracePtr - 1);
        $phpcsFile->fixer->endChangeset();
    }

    /**
     * Fixes the return value of a function to $this.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The php cs file
     * @param int $returnPtr Pointer to the return token
     *
     * @return void
     */
    private function fixMustReturnThis(PHP_CodeSniffer_File $phpcsFile, $returnPtr)
    {
        $returnSemicolonPtr = $phpcsFile->findEndOfStatement($returnPtr);

        for ($i = $returnPtr + 1; $i < $returnSemicolonPtr; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->addContentBefore(
            $returnSemicolonPtr,
            ' $this'
        );
        $phpcsFile->fixer->endChangeset();
    }
}
