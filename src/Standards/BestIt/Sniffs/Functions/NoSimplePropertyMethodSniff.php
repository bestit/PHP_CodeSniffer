<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\SuppressingTrait;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function current;
use function is_int;
use function preg_match;
use function substr;
use function trim;
use function ucfirst;
use const T_AS;
use const T_CONST;
use const T_FUNCTION;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_STRING;
use const T_VAR;
use const T_VARIABLE;

/**
 * We want to encourage that you use the new typed properties instead of redundant simple getter and setters.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class NoSimplePropertyMethodSniff extends AbstractSniff
{
    use SuppressingTrait;

    /**
     * The error code for the getter.
     *
     * @var string
     */
    public const CODE_SHOULD_USE_PROPERTY = 'ShouldUseTypedPropertyDirectly';

    /**
     * Error message when a simple getter exists.
     *
     * @var string
     */
    private const ERROR_GETTER_TOO_SIMPLE = 'We suggest that you use the typed property "%s" directly.';

    /**
     * Error message when a simple getter exists.
     *
     * @var string
     */
    private const ERROR_SETTER_TOO_SIMPLE = 'We suggest that you use the typed property "%s" directly.';

    /**
     * Method getter for the setter methods.
     *
     * @var string
     */
    private const METHOD_PREFIX_GETTER = 'get';

    /**
     * Method getter for the setter methods.
     *
     * @var string
     */
    private const METHOD_PREFIX_SETTER = 'set';

    /**
     * Finds the position of the method token which is named like the given string.
     *
     * @param string $string
     *
     * @return int|null
     */
    private function findMethod(string $string): ?int
    {
        $file = $this->getFile();
        $searchPos = $this->getStackPos();

        do {
            $searchPos = $file->findNext(T_FUNCTION, ++$searchPos);

            $nextPos = $searchPos + 1;
            $possibleNextName = $nextPos + 2;
        } while ($searchPos && !$file->findNext(T_STRING, $nextPos, $possibleNextName, false, $string));

        return is_int($searchPos) ? $searchPos : null;
    }

    /**
     * Returns the stack position of the relevant property if it is a typed one.
     *
     * @return int|null
     */
    private function getExactPropertyPointer(): ?int
    {
        $asPointer = TokenHelper::findPreviousEffective($this->getFile(), $this->getStackPos() - 1);

        if ($this->tokens[$asPointer]['code'] === T_AS) {
            return null;
        }

        $propertyPointer = TokenHelper::findNext(
            $this->getFile(),
            [T_FUNCTION, T_CONST, T_VARIABLE],
            $this->getStackPos() + 1,
        );

        if ($this->tokens[$propertyPointer]['code'] !== T_VARIABLE) {
            return null;
        }

        $propertyTypeHint = PropertyHelper::findTypeHint($this->getFile(), $propertyPointer);

        if (!$propertyTypeHint) {
            return null;
        }

        return $propertyPointer;
    }

    /**
     * Returns true if this sniff or a rule of this sniff is suppressed with the slevomat suppress annotation.
     *
     * @param null|string $rule The optional rule.
     * @param int|null $stackPos Do you want ot overload the position for the which position the sniff is suppressed.
     *
     * @return bool Returns true if the sniff is suppressed.
     */
    protected function isSniffSuppressed(?string $rule = null, ?int $stackPos = null): bool
    {
        return $this->getSuppressHelper()->isSniffSuppressed(
            $this->getFile(),
            $stackPos ?? $this->getStackPos(),
            $this->getSniffName($rule),
        );
    }

    /**
     * Returns true if the getter is too simple and does not provide more functionality.
     *
     * @param string $propertyName
     * @param int $methodPosition
     *
     * @return bool
     */
    private function isTooSimpleGetter(string $propertyName, int $methodPosition): bool
    {
        return $this->matchesMethodContentToRegex(
            '/return \$this->' . $propertyName . '\s*;$/m',
            $methodPosition,
        );
    }

    /**
     * Returns true if the setter is too simple and does not provide more functionality.
     *
     * @param string $propertyName
     * @param int $methodPosition
     *
     * @return bool
     */
    private function isTooSimpleSetter(string $propertyName, int $methodPosition): bool
    {
        $isTooSimpleSetter = false;
        $file = $this->getFile();

        $parameters = FunctionHelper::getParametersNames($file, $methodPosition);

        if (count($parameters) === 1) {
            $parameterName = current($parameters);

            $isTooSimpleSetter = $this->matchesMethodContentToRegex(
                '/\$this->' . $propertyName . '\s*=\s*\\' . $parameterName . '\s*;' .
                // finish with the assignment or wait for the fluent return.
                '($|\s*return\s*\$this\s*;$)/m',
                $methodPosition,
            );
        }

        return $isTooSimpleSetter;
    }

    /**
     * Returns true if the methods content matches the given regex.
     *
     * @param string $contentRegex
     *
     * @return bool
     */
    private function matchesMethodContentToRegex(string $contentRegex, int $methodPosition): bool
    {
        $matches = false;
        $methodToken = $this->tokens[$methodPosition];

        if ($methodToken && @$methodToken['scope_opener'] && @$methodToken['scope_closer']) {
            $fileContent = trim($this->getFile()->getTokensAsString(
                $methodToken['scope_opener'] + 1,
                $methodToken['scope_closer'] - $methodToken['scope_opener'] - 1,
            ));

            $matches = (bool) preg_match($contentRegex, $fileContent);
        }

        return $matches;
    }

    /**
     * Checks if the sniff on a typed property is not suppressed to check for a simple getter and setter.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $propertyPointer = $this->getExactPropertyPointer();

        if (!($propertyPointer && !$this->isSniffSuppressed(static::CODE_SHOULD_USE_PROPERTY))) {
            return;
        }

        $propertyName = substr($this->tokens[$propertyPointer]['content'], 1);
        $setterPosition = $this->findMethod(self::METHOD_PREFIX_SETTER . ucfirst($propertyName));
        $getterPosition = $this->findMethod(self::METHOD_PREFIX_GETTER . ucfirst($propertyName));

        if (
            $getterPosition && $setterPosition && $this->isTooSimpleSetter($propertyName, $setterPosition) &&
            $this->isTooSimpleGetter($propertyName, $getterPosition)
        ) {
            $this->getFile()->addWarning(
                self::ERROR_GETTER_TOO_SIMPLE,
                $getterPosition,
                static::CODE_SHOULD_USE_PROPERTY,
                $propertyName,
            );

            $this->getFile()->addWarning(
                self::ERROR_SETTER_TOO_SIMPLE,
                $setterPosition,
                static::CODE_SHOULD_USE_PROPERTY,
                $propertyName,
            );
        }
    }

    /**
     * Registers on the prefixes of a possible property, because is not so easy anymore since type hinting.
     *
     * @return array|mixed[]
     */
    public function register()
    {
        return [
            T_VAR,
            T_PUBLIC,
            T_PROTECTED,
            T_PRIVATE,
        ];
    }
}
