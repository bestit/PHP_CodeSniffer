<?php

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
     * @inheritdoc
     */
    protected function processTokenWithinScope(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $currScope
    ) {
        $className = $phpcsFile->getDeclarationName($currScope);
        $methodName = $phpcsFile->getDeclarationName($stackPtr);

        //Ignore closure functions
        if ($methodName === null) {
            return;
        }

        if (!$this->checkIfSetterFunction($methodName)) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $errorData = sprintf('%s::%s', $className, $methodName);

        $functionToken = $tokens[$stackPtr];
        $openBracePointer = $functionToken['scope_opener'];
        $closeBracePointer = $functionToken['scope_closer'];

        $returnPointer = $phpcsFile->findNext(T_RETURN, $openBracePointer, $closeBracePointer);

        if ($returnPointer === false) {
            $fixNoReturnFound = $phpcsFile->addFixableError(
                self::ERROR_NO_RETURN_FOUND,
                $stackPtr,
                self::CODE_NO_RETURN_FOUND,
                $errorData
            );

            if ($fixNoReturnFound) {
                $this->fixNoReturnFound($phpcsFile, $closeBracePointer);
            }

            return;
        }

        $followingReturnPointer = $phpcsFile->findNext(
            T_RETURN,
            $returnPointer + 1,
            $closeBracePointer
        );

        if ($followingReturnPointer !== false) {
            $phpcsFile->addError(
                self::ERROR_MULTIPLE_RETURN_FOUND,
                $stackPtr,
                self::CODE_MULTIPLE_RETURN_FOUND,
                $errorData
            );
            return;
        }

        $thisVariablePointer = $phpcsFile->findNext(T_VARIABLE, $returnPointer, null, false, '$this', true);

        if ($thisVariablePointer === false) {
            $fixMustReturnThis = $phpcsFile->addFixableError(
                self::ERROR_MUST_RETURN_THIS,
                $stackPtr,
                self::CODE_MUST_RETURN_THIS,
                $errorData
            );

            if ($fixMustReturnThis) {
                $this->fixMustReturnThis($phpcsFile, $returnPointer);
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
     * @param string $methodName
     *
     * @return bool
     */
    private function checkIfSetterFunction($methodName)
    {
        $firstUpperCasePosition = strcspn($methodName, 'ABCDEFGHJIJKLMNOPQRSTUVWXYZ');

        return substr($methodName, 0, $firstUpperCasePosition) === 'set';
    }

    /**
     * Fixes if no return statement is found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $closingCurlyBracePointer
     *
     * @return void
     */
    private function fixNoReturnFound(PHP_CodeSniffer_File $phpcsFile, $closingCurlyBracePointer)
    {
        $tokens = $phpcsFile->getTokens();
        $closingCurlyBraceToken = $tokens[$closingCurlyBracePointer];

        $expectedReturnSpaces = str_repeat($this->identation, $closingCurlyBraceToken['level'] + 1);

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->addNewlineBefore($closingCurlyBracePointer - 1);
        $phpcsFile->fixer->addContentBefore($closingCurlyBracePointer - 1, $expectedReturnSpaces . 'return $this;');
        $phpcsFile->fixer->addNewlineBefore($closingCurlyBracePointer - 1);
        $phpcsFile->fixer->endChangeset();
    }

    /**
     * Fixes the return value of a function to $this.
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int $returnPointer
     *
     * @return void
     */
    private function fixMustReturnThis(PHP_CodeSniffer_File $phpcsFile, $returnPointer)
    {
        $returnSemicolonPointer = $phpcsFile->findEndOfStatement($returnPointer);

        for ($i = $returnPointer + 1; $i < $returnSemicolonPointer; $i++) {
            $phpcsFile->fixer->replaceToken($i, '');
        }

        $phpcsFile->fixer->beginChangeset();
        $phpcsFile->fixer->addContentBefore(
            $returnSemicolonPointer,
            ' $this'
        );
        $phpcsFile->fixer->endChangeset();
    }
}
