<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints;

use BestIt\CodeSniffer\File;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\DocPosProviderTrait;
use BestIt\Sniffs\FunctionRegistrationTrait;
use BestIt\Sniffs\SuppressingTrait;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function strpos;
use function substr;

/**
 * Class ReturnTypeDeclarationSniff
 *
 * @author Stephan Weber <stephan.weber@bestit-online.de>
 * @package BestIt\Sniffs\TypeHints
 */
class ReturnTypeDeclarationSniff extends AbstractSniff
{
    use DocPosProviderTrait;
    use FunctionRegistrationTrait;
    use SuppressingTrait;

    /**
     * The error code for this sniff.
     *
     * @var string
     */
    public const CODE_MISSING_RETURN_TYPE_HINT = 'MissingReturnTypeHint';

    /**
     * Returns true if this sniff may run.
     *
     * @return bool
     */
    protected function areRequirementsMet(): bool
    {
        return !$this->isSniffSuppressed(static::CODE_MISSING_RETURN_TYPE_HINT) && !$this->hasInheritdocAnnotation();
    }

    /**
     * Check method type hints based on return annotation.
     *
     * @return void
     */
    protected function processToken(): void
    {
        if ($this->hasReturnType()) {
            return;
        }

        $returnAnnotation = FunctionHelper::findReturnAnnotation($this->file->getBaseFile(), $this->stackPos);
        $hasReturnAnnotation = $this->hasReturnAnnotation($returnAnnotation);
        $returnTypeHintDef = '';

        if ($hasReturnAnnotation) {
            $returnTypeHintDef = preg_split('~\\s+~', $returnAnnotation->getContent())[0];
        }

        $returnsValue = $this->returnsValue($hasReturnAnnotation, $returnTypeHintDef);

        if (!$hasReturnAnnotation && $returnsValue) {
            $this->file->addError(
                sprintf(
                    '%s %s() does not have return type hint nor @return annotation for its return value.',
                    $this->getFunctionTypeLabel($this->file, $this->stackPos),
                    FunctionHelper::getFullyQualifiedName($this->file->getBaseFile(), $this->stackPos)
                ),
                $this->stackPos,
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

            $fixable = $this->file->addFixableError(
                sprintf(
                    '%s %s() does not have return type hint for its return value'
                    . ' but it should be possible to add it based on @return annotation "%s".',
                    $this->getFunctionTypeLabel($this->file, $this->stackPos),
                    FunctionHelper::getFullyQualifiedName($this->file->getBaseFile(), $this->stackPos),
                    $returnTypeHintDef
                ),
                $this->stackPos,
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

        if (!FunctionHelper::isAbstract($this->file->getBaseFile(), $this->stackPos)) {
            $returnsValue = FunctionHelper::returnsValue($this->file->getBaseFile(), $this->stackPos);
        }

        return $returnsValue;
    }

    /**
     * Check if type hint sniff should be suppressed
     *
     * @return bool Suppressed or not
     */
    private function hasReturnType(): bool
    {
        return FunctionHelper::findReturnTypeHint($this->file->getBaseFile(), $this->stackPos) !== null;
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
            $this->file->fixer->beginChangeset();
            $returnTypeHint = $possibleReturnType;

            if (TypeHintHelper::isSimpleTypeHint($possibleReturnType)) {
                $returnTypeHint = TypeHintHelper::convertLongSimpleTypeHintToShort($possibleReturnType);
            }

            if (substr($returnTypeHint, -2) === '[]') {
                $returnTypeHint = 'array';
            }

            $this->file->fixer->addContent(
                $this->token['parenthesis_closer'],
                sprintf(': %s%s', ($nullableReturnType ? '?' : ''), $returnTypeHint)
            );
            $this->file->fixer->endChangeset();
        }
    }

    /**
     * Check if method has an inheritdoc annotation
     *
     * @return bool Has inheritdoc
     */
    private function hasInheritdocAnnotation(): bool
    {
        $return = false;

        if ($this->getDocCommentPos()) {
            $docBlockContent = trim($this->getDocHelper()->getBlockStartToken()['content']);

            $return = strpos($docBlockContent, '@inheritdoc') !== false;
        }

        return $return;
    }

    /**
     * Check if method or function
     *
     * @param File $file The php cs file
     * @param int $functionPointer The current token
     *
     * @return string Returns either Method or Function
     */
    private function getFunctionTypeLabel(File $file, int $functionPointer): string
    {
        return FunctionHelper::isMethod($file->getBaseFile(), $functionPointer) ? 'Method' : 'Function';
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
        return TypeHintHelper::isSimpleTypeHint($typeHint)
            || !TypeHintHelper::isSimpleUnofficialTypeHints($typeHint);
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
}
