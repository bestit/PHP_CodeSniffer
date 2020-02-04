<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\CodeSniffer\File as FileDecorator;
use BestIt\CodeSniffer\Helper\PropertyHelper;
use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\Sniffs\SuppressingTrait;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope\MethodScopeSniff;
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
    use SuppressingTrait;

    /**
     * Every setter function MUST return $this if nothing else is returned.
     */
    const CODE_MUST_RETURN_THIS = 'MustReturnThis';

    /**
     * Your method MUST contain a return.
     */
    const CODE_NO_RETURN_FOUND = 'NoReturnFound';

    /**
     * Error message when the method does not return $this.
     */
    const ERROR_MUST_RETURN_THIS = 'The method "%s" must return $this';

    /**
     * Error message when no return statement is found.
     */
    const ERROR_NO_RETURN_FOUND = 'Method "%s" has no return statement';

    /**
     * Specifies how an identation looks like.
     *
     * @var string
     */
    public $identation = '    ';

    /**
     * The used file decorated for the interface.
     *
     * @var FileDecorator
     */
    private $file;

    /**
     * The position of this node.
     *
     * @var int
     */
    private $stackPos;

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
    ) {
        $nextToken = $file->getTokens()[TokenHelper::findNextEffective($file, $returnPos + 1)];

        if (!$nextToken || (in_array($nextToken['content'], ['null', ';']))) {
            $fixMustReturnThis = $file->addFixableError(
                self::ERROR_MUST_RETURN_THIS,
                $functionPos,
                static::CODE_MUST_RETURN_THIS,
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
    private function checkForFluentSetterErrors(File $phpcsFile, int $functionPos, int $classPos)
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
                static::CODE_NO_RETURN_FOUND,
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
     * Returns the used file decorated for the interface.
     *
     * @return FileDecorator
     */
    public function getFile(): FileDecorator
    {
        return $this->file;
    }

    /**
     * Returns the position of this node.
     *
     * @return int
     */
    public function getStackPos(): int
    {
        return $this->stackPos;
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
    ) {
        $this->file = new FileDecorator($file);
        $this->stackPos = $functionPos;

        $isSuppressed = $this->isSniffSuppressed(static::CODE_NO_RETURN_FOUND);

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
    private function fixNoReturnFound(File $phpcsFile, int $closingBracePtr)
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
    private function fixMustReturnThis(File $phpcsFile, int $returnPtr)
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
