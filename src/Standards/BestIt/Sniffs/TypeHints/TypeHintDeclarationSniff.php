<?php

declare(strict_types = 1);

namespace BestIt\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff as BaseSniff;

/**
 * Class TypeHintDeclarationSniff
 *
 * @package BestIt\Sniffs\TypeHints
 * @author Stephan Weber <stephan.weber@bestit-online.de>
 */
class TypeHintDeclarationSniff extends BaseSniff
{
    /**
     * The php cs file
     *
     * @var File
     */
    private $phpcsFile;

    /**
     * The current token index
     *
     * @var int
     */
    private $pointer;

    /**
     * The current token
     *
     * @var array
     */
    private $token;

    /**
     * The SuppressHelper class
     *
     * @var SuppressHelper
     */
    private $suppressHelper;

    /**
     * The FunctionHelper class
     *
     * @var FunctionHelper
     */
    private $functionHelper;

    /**
     * The TypeHintHelper class
     *
     * @var TypeHintHelper
     */
    private $typeHintHelper;

    /**
     * The DocCommentHelper class
     *
     * @var DocCommentHelper
     */
    private $docCommentHelper;

    /**
     * TypeHintDeclarationSniff constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->enableVoidTypeHint = false;
        $this->enableNullableTypeHints = false;

        $this->suppressHelper = new SuppressHelper();
        $this->functionHelper = new FunctionHelper();
        $this->typeHintHelper = new TypeHintHelper();
        $this->docCommentHelper = new DocCommentHelper();
    }

    /**
     * Processes the phpcs file.
     *
     * @param File $phpcsFile The php cs file
     * @param int $pointer The current token index
     *
     * @return void
     */
    public function process(File $phpcsFile, $pointer)
    {
        $token = $phpcsFile->getTokens()[$pointer];

        $this->phpcsFile = $phpcsFile;
        $this->pointer = $pointer;
        $this->token = $token;

        $isSniffSuppressed = $this->suppressHelper::isSniffSuppressed($phpcsFile, $pointer, self::NAME);
        $hasInheritedDoc = $this->hasInheritdocAnnotation($phpcsFile, $pointer);

        if ($token['code'] === T_FUNCTION && !$isSniffSuppressed && !$hasInheritedDoc) {
            $this->checkReturnTypeHints($phpcsFile, $pointer);
        }
    }

    /**
     * Check method type hints based on return annotation.
     *
     * @param File $phpcsFile The php cs file
     * @param int $functionPointer The current token index
     *
     * @return void
     */
    private function checkReturnTypeHints(File $phpcsFile, int $functionPointer)
    {
        if ($this->returnTypeHintSuppressed()) {
            return;
        }

        $returnAnnotation = $this->functionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
        $hasReturnAnnotation = $this->hasReturnAnnotation($returnAnnotation);
        $returnTypeHintDef = '';

        if ($hasReturnAnnotation) {
            $returnTypeHintDef = preg_split('~\\s+~', $returnAnnotation->getContent())[0];
        }

        $returnsValue = $this->returnsValue($hasReturnAnnotation, $returnTypeHintDef);

        if (!$hasReturnAnnotation && $returnsValue) {
            $this->phpcsFile->addError(
                sprintf(
                    '%s %s() does not have return type hint nor @return annotation for its return value.',
                    $this->getFunctionTypeLabel($this->phpcsFile, $this->pointer),
                    $this->functionHelper::getFullyQualifiedName($this->phpcsFile, $this->pointer)
                ),
                $this->pointer,
                self::CODE_MISSING_RETURN_TYPE_HINT
            );
        }

        if (!$hasReturnAnnotation || !$returnsValue) {
            return;
        }

        $oneTypeHintDef = $this->definitionContainsOneTypeHint($returnTypeHintDef);
        $isValidTypeHint = $this->isValidTypeHint($returnTypeHintDef);

        if ($oneTypeHintDef && $isValidTypeHint) {
            $possibleReturnType = $returnTypeHintDef;
            $nullableReturnType = false;

            $fixable = $this->phpcsFile->addFixableError(
                sprintf(
                    '%s %s() does not have return type hint for its return value'
                    . ' but it should be possible to add it based on @return annotation "%s".',
                    $this->getFunctionTypeLabel($this->phpcsFile, $this->pointer),
                    $this->functionHelper::getFullyQualifiedName($this->phpcsFile, $this->pointer),
                    $returnTypeHintDef
                ),
                $this->pointer,
                self::CODE_MISSING_RETURN_TYPE_HINT
            );

            $this->fixTypeHint(
                $fixable,
                $possibleReturnType,
                $nullableReturnType
            );
        }
    }

    /**
     * Checks if the function returns a value
     *
     * @param bool $hasReturnAnnotation Function has a return annotation
     * @param string $returnTypeHintDef Return annotation type
     *
     * @return bool Function returns a value other than void
     */
    private function returnsValue(bool $hasReturnAnnotation, string $returnTypeHintDef): bool
    {
        $returnsValue = ($hasReturnAnnotation && $returnTypeHintDef !== 'void');

        if (!$this->functionHelper::isAbstract($this->phpcsFile, $this->pointer)) {
            $returnsValue = $this->functionHelper::returnsValue($this->phpcsFile, $this->pointer);
        }

        return $returnsValue;
    }

    /**
     * Check if type hint sniff should be suppressed
     *
     * @return bool Suppressed or not
     */
    private function returnTypeHintSuppressed(): bool
    {
        return (
            $this->functionHelper::findReturnTypeHint($this->phpcsFile, $this->pointer) !== null
            || $this->suppressHelper::isSniffSuppressed(
                $this->phpcsFile,
                $this->pointer,
                $this->getSniffName(self::CODE_MISSING_RETURN_TYPE_HINT)
            )
        );
    }

    /**
     * Check if function has a return annotation
     *
     * @param Annotation|null $returnAnnotation Annotation of the function
     *
     * @return bool Function has a annotation
     */
    private function hasReturnAnnotation($returnAnnotation): bool
    {
        return $returnAnnotation !== null && $returnAnnotation->getContent() !== null;
    }

    /**
     * Fixes the type hint error
     *
     * @param bool $fix Error is fixable
     * @param string $possibleReturnType Return annotation value
     * @param bool $nullableReturnType Is the return type nullable
     *
     * @return void
     */
    private function fixTypeHint(bool $fix, string $possibleReturnType, bool $nullableReturnType)
    {
        if ($fix) {
            $this->phpcsFile->fixer->beginChangeset();
            $returnTypeHint = $possibleReturnType;

            if ($this->typeHintHelper::isSimpleTypeHint($possibleReturnType)) {
                $returnTypeHint = $this->typeHintHelper::convertLongSimpleTypeHintToShort($possibleReturnType);
            }

            $this->phpcsFile->fixer->addContent(
                $this->token['parenthesis_closer'],
                sprintf(': %s%s', ($nullableReturnType ? '?' : ''), $returnTypeHint)
            );
            $this->phpcsFile->fixer->endChangeset();
        }
    }

    /**
     * Check if method has an inheritdoc annotation
     *
     * @param File $phpcsFile The php cs file
     * @param int $functionPointer The current token
     *
     * @return bool Has inheritdoc
     */
    private function hasInheritdocAnnotation(File $phpcsFile, int $functionPointer): bool
    {
        $docComment = $this->docCommentHelper::getDocComment($phpcsFile, $functionPointer);

        if ($docComment === null) {
            return false;
        }

        return stripos($docComment, '@inheritdoc') !== false;
    }

    /**
     * Check if method or function
     *
     * @param File $phpcsFile The php cs file
     * @param int $functionPointer The current token
     *
     * @return string Returns either Method or Function
     */
    private function getFunctionTypeLabel(File $phpcsFile, int $functionPointer): string
    {
        return $this->functionHelper::isMethod($phpcsFile, $functionPointer) ? 'Method' : 'Function';
    }

    /**
     * Check if the return annotation type is valid.
     *
     * @param string $typeHint The type value of the return annotation
     *
     * @return bool Type is valid
     */
    private function isValidTypeHint(string $typeHint): bool
    {
        return $this->typeHintHelper::isSimpleTypeHint($typeHint)
            || !$this->typeHintHelper::isSimpleUnofficialTypeHints($typeHint);
    }

    /**
     * Check if return annotation has only one type.
     *
     * @param string $typeHintDefinition The type values of the return annotation
     *
     * @return bool Return annotation has only one type
     */
    private function definitionContainsOneTypeHint(string $typeHintDefinition): bool
    {
        return strpos($typeHintDefinition, '|') === false;
    }

    /**
     * Get the sniff name.
     *
     * @param string $sniffName The name of the sniff (const name)
     *
     * @return string Returns sniff name with prefixed class name
     */
    private function getSniffName(string $sniffName): string
    {
        return sprintf('%s.%s', self::NAME, $sniffName);
    }
}
