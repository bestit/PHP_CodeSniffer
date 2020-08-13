<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\Helper\TokenHelper;
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
     * You SHOULD provide a description for your parameter.
     */
    public const CODE_TAG_MISSING_DESC = 'MissingDesc';

    /**
     * You MUST provide a type for your param tag.
     */
    public const CODE_TAG_MISSING_TYPE = 'MissingType';

    /**
     * Your method MUST have a matching variable for your param tag.
     */
    public const CODE_TAG_MISSING_VARIABLE = 'MissingVariable';

    /**
     * Your method MUST have parameters if there is a param tag.
     */
    public const CODE_TAG_MISSING_VARIABLES = 'MissingVariables';

    /**
     * You SHOULD prevent the mixed type and try to provide native types.
     */
    public const CODE_TAG_MIXED_TYPE = 'MixedType';

    /**
     * Message for displaying the missing description.
     */
    private const MESSAGE_TAG_MISSING_DESC = 'There is no description for your tag: %s.';

    /**
     * Message for displaying the missing type.
     */
    private const MESSAGE_TAG_MISSING_TYPE = 'There is no type for your tag: %s.';

    /**
     * Message for displaying the missing property.
     */
    private const MESSAGE_TAG_MISSING_VARIABLE = 'There is no property for your tag "%s".';

    /**
     * Message for displaying the missing properties.
     */
    private const MESSAGE_TAG_MISSING_VARIABLES = 'There are no properties for your tags.';

    /**
     * The message for the mixed type warning.
     */
    private const MESSAGE_TAG_MIXED_TYPE = 'We suggest that you avoid the "mixed" type and declare the ' .
        'required types in detail.';

    /**
     * The token of the real function argument if it exists or null.
     *
     * @var array|null Is filled by runtime.
     */
    private ?array $argumentToken = null;

    /**
     * Should the missing description emit a warning?
     *
     * @var bool
     */
    public bool $descAsWarning = false;

    /**
     * The used variable tokens for this method.
     *
     * @var array
     */
    protected array $varTokens;

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
            throw (new CodeError(static::CODE_TAG_MISSING_VARIABLE, self::MESSAGE_TAG_MISSING_VARIABLE, $this->stackPos))
                ->setPayload([$tagContent])
                ->setToken($this->token);
        }

        return $return;
    }

    /**
     * Checks if the argument of the function itself exists.
     *
     * @param null|string $tagContent
     * @throws CodeWarning
     *
     * @return void
     */
    private function checkArgumentItself(?string $tagContent = null): void
    {
        if (!$this->getArgumentTokenOfTag()) {
            throw (new CodeError(
                static::CODE_TAG_MISSING_VARIABLE,
                self::MESSAGE_TAG_MISSING_VARIABLE,
                $this->stackPos,
            ))
                ->setPayload([$tagContent])
                ->setToken($this->token);
        }
    }

    /**
     * Checks if the param contains a description.
     *
     * @throws CodeWarning
     *
     * @return bool|null Returns true if there is a desc.
     */
    private function checkDescription(): bool
    {
        if (($hasNoDesc = !@$this->matches['desc']) && ($this->descAsWarning)) {
            throw (new CodeWarning(static::CODE_TAG_MISSING_DESC, self::MESSAGE_TAG_MISSING_DESC, $this->stackPos))
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
            throw (new CodeError(static::CODE_TAG_MISSING_TYPE, self::MESSAGE_TAG_MISSING_TYPE, $this->stackPos))
                ->setPayload([$this->matches['var']])
                ->setToken($this->token);
        }

        if (strtolower($this->matches['type']) === 'mixed') {
            throw (new CodeWarning(static::CODE_TAG_MIXED_TYPE, self::MESSAGE_TAG_MIXED_TYPE, $this->stackPos))
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
            $this->file,
            [T_VARIABLE],
            $this->stackPos + 1,
            $this->file->findNext([T_CLOSE_PARENTHESIS], $this->stackPos + 1),
        );
    }

    /**
     * Returns a pattern to check if the content is valid.
     *
     * @return string The pattern which matches successful.
     */
    protected function getValidPattern(): string
    {
        $varOfThisTag = $this->getArgumentTokenOfTag();

        return '/(?P<type>[\w|\|\[\]]*) ?(?P<var>' . preg_quote($varOfThisTag['content'], '/') .
            ') ?(?P<desc>.*)/m';
    }

    /**
     * Returns the name of the real function argument for this parameter tag.
     *
     * @return array Null if there is no matching function argument.
     */
    private function getArgumentTokenOfTag(): array
    {
        if ($this->argumentToken === null) {
            $this->argumentToken = $this->loadArgumentTokenOfTag();
        }

        return $this->argumentToken;
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
            throw (new CodeError(
                static::CODE_TAG_MISSING_VARIABLES,
                self::MESSAGE_TAG_MISSING_VARIABLES,
                $this->stackPos,
            ))->setToken($this->token);
        }

        $this->varTokens = array_filter($this->tokens, function (array $token) use ($varPositions): bool {
            return in_array($token['pointer'], $varPositions, true);
        });

        return $varPositions;
    }

    /**
     * Loads the function argument of this tag.
     *
     * @return array
     */
    private function loadArgumentTokenOfTag(): array
    {
        // Give me the other tags of this doc block before this one.
        $tagPosBeforeThis = TokenHelper::findNextAll(
            $this->file,
            $this->register(),
            $this->file->findPrevious([T_DOC_COMMENT_OPEN_TAG], $this->stackPos),
            $this->stackPos - 1,
        );

        $tagPosBeforeThis = array_filter($tagPosBeforeThis, function (int $position) {
            return $this->tokens[$position]['content'] === '@param';
        });

        return (array_values($this->varTokens)[count($tagPosBeforeThis)]) ?? [];
    }

    /**
     * Processed the content of the required tag.
     *
     * @param null|string $tagContent The possible tag content or null.
     * @throws CodeWarning
     *
     * @return void
     */
    protected function processTagContent(?string $tagContent = null): void
    {
        $varPoss = $this->loadAndCheckVarPositions();

        if ($varPoss) {
            $this->checkArgumentItself($tagContent);
            $this->checkAgainstPattern($tagContent);
            $this->checkType();
            $this->checkDescription();
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

    /**
     * Sets up the test and loads the matching var if there is one.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->addPointerToTokens();

        $this->argumentToken = null;
    }
}
