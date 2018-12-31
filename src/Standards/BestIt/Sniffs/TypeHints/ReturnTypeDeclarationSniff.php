<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\DocPosProviderTrait;
use BestIt\Sniffs\FunctionRegistrationTrait;
use BestIt\Sniffs\SuppressingTrait;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_filter;
use function array_intersect;
use function count;
use function explode;
use function in_array;
use function phpversion;
use function strtolower;
use function substr;
use function version_compare;

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
     * Every function or method MUST have a type hint if the return annotation is valid.
     */
    public const CODE_MISSING_RETURN_TYPE = 'MissingReturnTypeHint';

    /**
     * The simple message of a return type is missing.
     */
    private const MESSAGE_MISSING_RETURN_TYPE = 'Function/Method %s does not have a return type.';

    /**
     * The return types which match null.
     */
    private const NULL_TYPES = ['null', 'void'];

    /**
     * Null as a return has no real return type, so we use this as a fallback.
     *
     * @var string
     */
    public $defaultNullReturn = '?string';

    /**
     * The name of the function.
     *
     * @var string|null
     */
    private $functionName = null;

    /**
     * Has this function a return type?
     *
     * @var null|bool
     */
    private $hasReturnType = null;

    /**
     * This methods should be ignored.
     *
     * @var array
     */
    public $methodsWithoutVoid = ['__construct', '__destruct', '__clone'];

    /**
     * Caches the types which can be used for an automatic fix.
     *
     * This array is only filled, if the return annotation situation of the phpdoc is usable for a fix.
     *
     * @var null|array
     */
    private $typesForFix = null;

    /**
     * Adds the return type to fix the error.
     *
     * @return void
     */
    private function addReturnType(): void
    {
        $file = $this->getFile();
        $returnTypeHint = $this->createReturnType();

        $file->fixer->beginChangeset();

        if ($this->isCustomArrayType($returnTypeHint)) {
            $returnTypeHint = ($returnTypeHint[0] === '?' ? '?' : '') . 'array';
        }

        $file->fixer->addContent(
            $this->token['parenthesis_closer'],
            ': ' . $returnTypeHint
        );

        $file->fixer->endChangeset();
    }

    /**
     * Returns true if this sniff may run.
     *
     * @return bool
     */
    protected function areRequirementsMet(): bool
    {
        return !$this->isSniffSuppressed(static::CODE_MISSING_RETURN_TYPE) && !$this->hasReturnType() &&
            !in_array($this->getFunctionName(), $this->methodsWithoutVoid);
    }

    /**
     * Creates the new return type for fixing or returns a null if not possible.
     *
     * @return string
     */
    private function createReturnType(): string
    {
        $returnTypeHint = '';
        $typeCount = count($this->typesForFix);

        foreach ($this->typesForFix as $type) {
            // We add the default value if only null is used (which has no real native return type).
            if ($type === 'null' && ($typeCount === 1)) {
                $returnTypeHint = $this->defaultNullReturn;
                break; // We still need this break to prevent further execution of the default value.
            }

            // We add the question mark if there is a nullable type.
            if (in_array($type, self::NULL_TYPES, true) && ($typeCount > 1)) {
                $returnTypeHint = '?' . $returnTypeHint;
                continue; // We still need this continue to prevent further execution of the questionmark.
            }

            // We add a fixable "native" type. We do not fix custom classes (because it would have side effects to the
            // imported usage of classes.
            $returnTypeHint .= (TypeHintHelper::isSimpleTypeHint($type))
                ? TypeHintHelper::convertLongSimpleTypeHintToShort($type)
                : $type;
        }

        return $returnTypeHint;
    }

    /**
     * Fixes the return type error.
     *
     * @param CodeWarning $exception
     *
     * @return void
     */
    protected function fixDefaultProblem(CodeWarning $exception): void
    {
        // Satisfy PHPMD
        unset($exception);

        // This method is called, if it the error is not marked as fixable. So check our internal marker again.
        if ($this->typesForFix) {
            $this->addReturnType();
        }
    }

    /**
     * Returns the name of the function.
     *
     * @return string
     */
    private function getFunctionName(): string
    {
        if (!$this->functionName) {
            $this->functionName = $this->loadFunctionName();
        }

        return $this->functionName;
    }

    /**
     * Returns the return types of the annotation.
     *
     * @param null|Annotation $annotation
     *
     * @return array
     */
    private function getReturnsFromAnnotation(?Annotation $annotation): array
    {
        return $this->isFilledReturnAnnotation($annotation)
            ? explode('|', preg_split('~\\s+~', $annotation->getContent())[0])
            : [];
    }

    /**
     * Returns the types of the annotation, if the types are usable.
     *
     * Usable means, that there should be one type != mixed in the return-annotation or a nullable type, which means
     * 2 types like null|$ANYTYPE.
     *
     * @param Annotation $annotation
     *
     * @return array|null Null if there are no usable types or the usable types.
     */
    private function getUsableReturnTypes(Annotation $annotation): ?array
    {
        $return = null;

        $returnTypes = $this->getReturnsFromAnnotation($annotation);
        $returnTypeCount = count($returnTypes);
        $justOneReturn = $returnTypeCount === 1;

        if (!$justOneReturn || strtolower($returnTypes[0]) !== 'mixed') {
            $isNullableType = ($returnTypeCount === 2) &&
                version_compare(phpversion(), '7.1.0', '>') &&
                (count(array_intersect($returnTypes, self::NULL_TYPES)) === 1);

            $return = ($justOneReturn || $isNullableType) ? $returnTypes : null;
        }

        return $return;
    }

    /**
     * Check if there is a return type.
     *
     * @return bool
     */
    private function hasReturnType(): bool
    {
        if ($this->hasReturnType === null) {
            $this->hasReturnType = FunctionHelper::hasReturnTypeHint($this->file->getBaseFile(), $this->stackPos);
        }

        return $this->hasReturnType;
    }

    /**
     * Is the given array a custom array with the "[]" suffix?
     *
     * @param string $returnTypeHint
     *
     * @return bool
     */
    private function isCustomArrayType(string $returnTypeHint): bool
    {
        return substr($returnTypeHint, -2) === '[]';
    }

    /**
     * Check if function has a return annotation
     *
     * @param Annotation|null $returnAnnotation Annotation of the function
     *
     * @return bool Function has a annotation
     */
    private function isFilledReturnAnnotation(?Annotation $returnAnnotation = null): bool
    {
        return $returnAnnotation && $returnAnnotation->getContent();
    }

    /**
     * Check if the return annotation type is valid.
     *
     * @param string $type The type value of the return annotation
     *
     * @return bool Type is valid
     */
    private function isFixableReturnType(string $type): bool
    {
        // $type === null is not valid in our slevomat helper.
        return TypeHintHelper::isSimpleTypeHint($type) || $type === 'null' || $this->isCustomArrayType($type);
    }

    /**
     * Removes the return types from the given array, which are not compatible with our fix.
     *
     * @param array|null $returnTypes
     *
     * @return array The cleaned array.
     */
    private function loadFixableTypes(?array $returnTypes): array
    {
        return array_filter($returnTypes ?? [], function (string $returnType): bool {
            return $this->isFixableReturnType($returnType);
        });
    }

    /**
     * Loads the name of the function.
     *
     * @return string
     */
    private function loadFunctionName(): string
    {
        return FunctionHelper::getName($this->getFile()->getBaseFile(), $this->stackPos);
    }

    /**
     * Loads the return annotation for this method.
     *
     * @return null|Annotation
     */
    protected function loadReturnAnnotation(): ?Annotation
    {
        return FunctionHelper::findReturnAnnotation($this->getFile()->getBaseFile(), $this->stackPos);
    }

    /**
     * Check method return type based with its return annotation.
     *
     * @throws CodeWarning
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this->getFile()->recordMetric($this->stackPos, 'Has return type', 'no');

        $returnAnnotation = $this->loadReturnAnnotation();

        if (!$this->isFilledReturnAnnotation($returnAnnotation) ||
            ($returnTypes = $this->getUsableReturnTypes($returnAnnotation))) {
            $this->validateReturnType($returnTypes ?? null);
        }
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        if ($this->hasReturnType()) {
            $this->getFile()->recordMetric($this->stackPos, 'Has return type', 'yes');
        }
    }

    /**
     * Resets the data of this sniff.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->resetDocCommentPos();

        $this->hasReturnType = null;
        $this->functionName = null;
        $this->typesForFix = null;
    }

    /**
     * Validates the return type and registers an error if there is one.
     *
     * @param array|null $returnTypes
     * @throws CodeWarning
     *
     * @return void
     */
    private function validateReturnType(?array $returnTypes): void
    {
        if (!$returnTypes) {
            $returnTypes = [];
        }

        $fixableTypes = $this->loadFixableTypes($returnTypes);

        if (count($returnTypes) === count($fixableTypes)) {
            // Make sure this var is only filled, if it really is fixable for us.
            $this->typesForFix = $fixableTypes;
        }

        $exception =
            (new CodeError(static::CODE_MISSING_RETURN_TYPE, self::MESSAGE_MISSING_RETURN_TYPE, $this->stackPos))
                ->setPayload([$this->getFunctionName()]);

        $exception->isFixable((bool) $this->typesForFix);

        throw $exception;
    }
}
