<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\CodeSniffer\File as FileDecorator;
use BestIt\CodeSniffer\Helper\PropertyHelper;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope\MethodScopeSniff;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function substr;

/**
 * Checks if a fluent setter is used per default.
 *
 * @package BestIt\Sniffs\Functions
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @author Björn Lange <bjoern.lange@bestit-online.de>
 */
class FluentSetterSniff extends MethodScopeSniff
{
    /**
     * Code when the method does not return $this.
     *
     * @var string
     */
    public const CODE_MUST_RETURN_THIS = 'MustReturnThis';

    /**
     * Code when no return statement is found.
     *
     * @var string
     */
    public const CODE_NO_RETURN_FOUND = 'NoReturnFound';

    /**
     * Error message when the method does not return $this.
     *
     * @var string
     */
    private const ERROR_MUST_RETURN_THIS = 'The method "%s" must return $this';

    /**
     * Error message when no return statement is found.
     *
     * @var string
     */
    private const ERROR_NO_RETURN_FOUND = 'Method "%s" has no return statement';

    /**
     * Specifies how an identation looks like.
     *
     * @var string
     */
    public $identation = '    ';

    /**
     * Registers an error if an empty return (return null; or return;) is given.
     *
     * @param File $file The sniffed file.
     * @param int $functionPos The position of the function.
     * @param int $returnPos The position of the return call.
     * @param string $methodIdent The ident for the method to given in an error.
     *
     * @return void
     */
    private function checkAndRegisterEmptyReturnErrors(
        File $file,
        int $functionPos,
        int $returnPos,
        string $methodIdent
    ): void {
        $nextToken = $file->getTokens()[TokenHelper::findNextEffective($file, $returnPos + 1)];

        if (!$nextToken || (in_array($nextToken['content'], ['null', ';']))) {
            $fixMustReturnThis = $file->addFixableError(
                self::ERROR_MUST_RETURN_THIS,
                $functionPos,
                self::CODE_MUST_RETURN_THIS,
                $methodIdent
            );

            if ($fixMustReturnThis) {
                $this->fixMustReturnThis($file, $returnPos);
            }
        }
    }

    /**
     * Checks if there are fluent setter errors and registers errors if needed.
     *
     * @param File $phpcsFile The file for this sniff.
     * @param int $functionPos The position of the used token.
     * @param int $classPos The position of the class.
     *
     * @return void
     */
    private function checkForFluentSetterErrors(File $phpcsFile, int $functionPos, int $classPos): void
    {
        $tokens = $phpcsFile->getTokens();
        $errorData = $phpcsFile->getDeclarationName($classPos) . '::' . $phpcsFile->getDeclarationName($functionPos);

        $functionToken = $tokens[$functionPos];
        $openBracePtr = $functionToken['scope_opener'];
        $closeBracePtr = $functionToken['scope_closer'];

        $returnPtr = $phpcsFile->findNext(T_RETURN, $openBracePtr, $closeBracePtr);

        if ($returnPtr === false) {
            $fixNoReturnFound = $phpcsFile->addFixableError(
                self::ERROR_NO_RETURN_FOUND,
                $functionPos,
                self::CODE_NO_RETURN_FOUND,
                $errorData
            );

            if ($fixNoReturnFound) {
                $this->fixNoReturnFound($phpcsFile, $closeBracePtr);
            }

            return;
        }

        $this->checkAndRegisterEmptyReturnErrors($phpcsFile, $functionPos, $returnPtr, $errorData);
    }

    /**
     * Get the sniff name.
     *
     * @param string $sniffName If there is an optional sniff name.
     *
     * @return string Returns the special sniff name in the code sniffer context.
     */
    private function getSniffName(string $sniffName = ''): string
    {
        $sniffFQCN = preg_replace(
            '/Sniff$/',
            '',
            str_replace(['\\', '.Sniffs'], ['.', ''], static::class)
        );

        if ($sniffName) {
            $sniffFQCN .= '.' . $sniffName;
        }

        return $sniffFQCN;
    }

    /**
     * Processes the tokens that this test is listening for.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param File $file The file where this token was found.
     * @param int $functionPos The position in the stack where this token was found.
     * @param int $classPos The position in the tokens array that opened the scope that this test is listening for.
     *
     * @return void
     */
    protected function processTokenWithinScope(
        File $file,
        $functionPos,
        $classPos
    ): void {
        $isSuppressed = SuppressHelper::isSniffSuppressed(
            $file,
            $functionPos,
            $this->getSniffName(static::CODE_NO_RETURN_FOUND)
        );

        if (!$isSuppressed && $this->checkIfSetterFunction($classPos, $file, $functionPos)) {
            $this->checkForFluentSetterErrors($file, $functionPos, $classPos);
        }
    }

    /**
     * Checks if the given method name relates to a setter function of a property.
     *
     * @param int $classPosition The position of the class token.
     * @param File $file The file of the sniff.
     * @param int $methodPosition The position of the method token.
     *
     * @return bool Indicator if the given method is a setter function
     */
    private function checkIfSetterFunction(int $classPosition, File $file, int $methodPosition): bool
    {
        $isSetter = false;
        $methodName = $file->getDeclarationName($methodPosition);

        if (substr($methodName, 0, 3) === 'set') {
            // We define in our styleguide, that there is only one class per file!
            $properties = (new PropertyHelper(new FileDecorator($file)))->getProperties(
                $file->getTokens()[$classPosition]
            );

            // We require camelCase for methods and properties,
            // so there should be an "lcfirst-Method" without set-prefix.
            $isSetter = in_array(lcfirst(substr($methodName, 3)), $properties, true);
        }

        return $isSetter;
    }

    /**
     * Fixes if no return statement is found.
     *
     * @param File $phpcsFile The php cs file
     * @param int $closingBracePtr Pointer to the closing curly brace of the function
     *
     * @return void
     */
    private function fixNoReturnFound(File $phpcsFile, int $closingBracePtr): void
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
     * @param File $phpcsFile The php cs file
     * @param int $returnPtr Pointer to the return token
     *
     * @return void
     */
    private function fixMustReturnThis(File $phpcsFile, int $returnPtr): void
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
