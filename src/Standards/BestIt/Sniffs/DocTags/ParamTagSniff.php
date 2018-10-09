<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_filter;
use function array_values;
use function in_array;
use function preg_quote;
use function strtolower;
use const T_CLOSE_PARENTHESIS;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_VARIABLE;

/**
 * Sniff for the param tag content.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class ParamTagSniff extends AbstractTagSniff
{
    use TagContentFormatTrait;

    /**
     * The error code for the missing description.
     *
     * @var string
     */
    public const CODE_TAG_MISSING_DESC = 'MissingDesc';

    /**
     * The error code if the type of the param tag is missing.
     *
     * @var string
     */
    public const CODE_TAG_MISSING_TYPE = 'MissingType';

    /**
     * The error code if the matching property is missing.
     *
     * @var string
     */
    public const CODE_TAG_MISSING_VARIABLE = 'MissingVariable';

    /**
     * The error code if every property is missing.
     *
     * @var string
     */
    public const CODE_TAG_MISSING_VARIABLES = 'MissingVariables';

    /**
     * Error code for the mixed type.
     *
     * @var string
     */
    public const CODE_TAG_MIXED_TYPE = 'MixedType';

    /**
     * Message for displaying the missing description.
     *
     * @var string
     */
    private const MESSAGE_TAG_MISSING_DESC = 'There is no description for your tag: %s.';

    /**
     * Message for displaying the missing type.
     *
     * @var string
     */
    private const MESSAGE_TAG_MISSING_TYPE = 'There is no type for your tag: %s.';

    /**
     * Message for displaying the missing property.
     *
     * @var string
     */
    private const MESSAGE_TAG_MISSING_VARIABLE = 'There is no property for your tag "%s".';

    /**
     * Message for displaying the missing properties.
     *
     * @var string
     */
    private const MESSAGE_TAG_MISSING_VARIABLES = 'There are no properties for your tags.';

    /**
     * The message for the mixed type warning.
     *
     * @var string
     */
    private const MESSAGE_TAG_MIXED_TYPE = 'We suggest that you avoid the "mixed" type and declare the ' .
        'required types in detail.';

    /**
     * The used variable tokens for this method.
     *
     * @var array
     */
    protected $varTokens;

    /**
     * Simple check if the pattern is correct.
     *
     * @param string|null $tagContent
     * @throws CodeWarning
     *
     * @return bool True if it matches.
     */
    private function checkAgainstPattern(?string $tagContent = null): bool
    {
        if (!$return = $this->isValidContent($tagContent)) {
            throw (new CodeError(self::CODE_TAG_MISSING_VARIABLE, self::MESSAGE_TAG_MISSING_VARIABLE, $this->stackPos))
                ->setPayload([$tagContent])
                ->setToken($this->token);
        }

        return $return;
    }

    /**
     * Checks if the param contains a description.
     *
     * @throws CodeWarning
     *
     * @return bool Returns true if there is a desc.
     */
    private function checkDescription(): bool
    {
        if ($hasNoDesc = !@$this->matches['desc']) {
            throw (new CodeWarning(self::CODE_TAG_MISSING_DESC, self::MESSAGE_TAG_MISSING_DESC, $this->stackPos))
                ->setPayload([$this->matches['var']])
                ->setToken($this->token);
        }

        return !$hasNoDesc;
    }

    /**
     * Checks if the param tag contains a valid type.
     *
     * @throws CodeWarning
     *
     * @return bool True if the type is valid.
     */
    private function checkType(): bool
    {
        if (!@$this->matches['type']) {
            throw (new CodeError(self::CODE_TAG_MISSING_TYPE, self::MESSAGE_TAG_MISSING_TYPE, $this->stackPos))
                ->setPayload([$this->matches['var']])
                ->setToken($this->token);
        }

        if (strtolower($this->matches['type']) === 'mixed') {
            throw (new CodeWarning(self::CODE_TAG_MIXED_TYPE, self::MESSAGE_TAG_MIXED_TYPE, $this->stackPos))
                ->setToken($this->token);
        }


        return true;
    }

    /**
     * Loads all variables of the following method.
     *
     * @return array
     */
    protected function findAllVariablePositions(): array
    {
        return TokenHelper::findNextAll(
            $this->file->getBaseFile(),
            [T_VARIABLE],
            $this->stackPos + 1,
            $this->file->findNext([T_CLOSE_PARENTHESIS], $this->stackPos + 1)
        );
    }

    /**
     * Returns a pattern to check if the content is valid.
     *
     * @return string The pattern which matches successful.
     */
    protected function getValidPattern(): string
    {
        // Give me the other tags of this doc block before this one.
        $tagPosBeforeThis = TokenHelper::findNextAll(
            $this->file->getBaseFile(),
            $this->register(),
            $this->file->findPrevious([T_DOC_COMMENT_OPEN_TAG], $this->stackPos),
            $this->stackPos - 1
        );

        $tagPosBeforeThis = array_filter($tagPosBeforeThis, function (int $position) {
            return $this->tokens[$position]['content'] === '@param';
        });

        $varOfThisTag = array_values($this->varTokens)[count($tagPosBeforeThis)];

        return '/(?P<type>[\w|\|\[\]]*) ?(?P<var>' . preg_quote($varOfThisTag['content'], '/') .
            ') ?(?P<desc>.*)/m';
    }

    /**
     * Loads and checks the variables of the following method.
     *
     * @throws CodeWarning We have param tags, so there should be variables in the method.
     *
     * @return array The positions of the methods variables if there are any.
     */
    private function loadAndCheckVarPositions(): array
    {
        $varPositions = $this->findAllVariablePositions();

        if (!$varPositions) {
            throw (new CodeError(self::CODE_TAG_MISSING_VARIABLES, self::MESSAGE_TAG_MISSING_VARIABLES, $this->stackPos))
                ->setToken($this->token);
        }

        $this->varTokens = array_filter($this->tokens, function (array $token) use ($varPositions): bool {
            return in_array($token['pointer'], $varPositions, true);
        });

        return $varPositions;
    }

    /**
     * Processed the content of the required tag.
     *
     * @param null|string $tagContent The possible tag content or null.
     *
     * @return void
     */
    protected function processTagContent(?string $tagContent = null): void
    {
        try {
            $varPoss = $this->loadAndCheckVarPositions();

            if ($varPoss) {
                $this->checkAgainstPattern($tagContent);
                $this->checkType();
                $this->checkDescription();
            }

            $this->getFile()->recordMetric(
                $this->getStackPos(),
                sprintf('Valid %s tag:', $this->registerTag()),
                'Yes'
            );
        } catch (CodeWarning $exception) {
            $this->getExceptionHandler()->handleException($exception);

            $this->getFile()->recordMetric(
                $this->getStackPos(),
                sprintf('Valid %s tag:', $this->registerTag()),
                'No'
            );
        }
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'param';
    }
}
