<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\Helper\PropertyHelper;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\DocPosProviderTrait;
use function array_keys;
use function lcfirst;
use function ucfirst;
use const T_CLASS;
use const T_CONST;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_TRAIT;
use const T_VARIABLE;

/**
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class RequiredDocBlockSniff extends AbstractSniff
{
    use DocPosProviderTrait;

    /**
     * The error code for missing doc blocks.
     */
    public const CODE_MISSING_DOC_BLOCK_PREFIX = 'MissingDocBlock';

    /**
     * The error code for the inline block.
     */
    public const CODE_NO_MULTI_LINE_DOC_BLOCK_PREFIX = 'NoMultiLineDocBlock';

    /**
     * The message for missing doc blocks.
     */
    private const MESSAGE_MISSING_DOC_BLOCK = 'Please provide a doc block for your %s.';

    /**
     * The error message for the inline block.
     */
    private const MESSAGE_NO_MULTI_LINE_DOC_BLOCK_PREFIX = 'Please provide a multi line doc block for your %s.';

    /**
     * Maps the registered tokens to a readable key.
     *
     * @var array
     */
    private $registeredTokens = [
        T_CLASS => 'Class',
        T_CONST => 'Constant',
        T_INTERFACE => 'Interface',
        T_FUNCTION => 'Function',
        T_TRAIT => 'Trait',
        T_VARIABLE => 'Variable'
    ];

    /**
     * Ignore normal variables for this sniff.
     *
     * @return bool
     */
    protected function areRequirementsMet(): bool
    {
        return ($this->token['code'] !== T_VARIABLE) || (new PropertyHelper($this->file))->isProperty($this->stackPos);
    }

    /**
     * Checks for a missing doc block and throws an error if the doc is missing.
     *
     * @throws CodeWarning
     *
     * @return void
     */
    private function checkAndRegisterMissingDocBlock(): void
    {
        if (!$this->getDocCommentPos()) {
            $tokenIdent = $this->getTokenName();

            $exception = (new CodeError(
                self::CODE_MISSING_DOC_BLOCK_PREFIX . ucfirst($tokenIdent),
                self::MESSAGE_MISSING_DOC_BLOCK,
                $this->stackPos
            ))->setPayload([lcfirst($tokenIdent)]);

            throw $exception;
        }
    }

    /**
     * Checks and registers a multi line error.
     *
     * @throws CodeWarning
     *
     * @return void
     */
    private function checkAndRegisterNoMultiLine(): void
    {
        $docCommentPos = $this->getDocCommentPos();
        $openingToken = $this->tokens[$docCommentPos];
        $closingToken = $this->tokens[$openingToken['comment_closer']];

        if ($openingToken['line'] === $closingToken['line']) {
            $tokenIdent = $this->getTokenName();

            $exception = (new CodeError(
                self::CODE_NO_MULTI_LINE_DOC_BLOCK_PREFIX . ucfirst($tokenIdent),
                self::MESSAGE_NO_MULTI_LINE_DOC_BLOCK_PREFIX,
                $docCommentPos
            ))->setPayload([lcfirst($tokenIdent)]);

            throw $exception;
        }
    }

    /**
     * Returns the name for the token.
     *
     * @return string
     */
    private function getTokenName(): string
    {
        return $this->registeredTokens[$this->token['code']];
    }

    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        try {
            $this->checkAndRegisterMissingDocBlock();
            $this->checkAndRegisterNoMultiLine();
        } catch (CodeWarning $error) {
            $this->getExceptionHandler()->handleException($error);
        }
    }

    /**
     * Register for all tokens which require a php doc block for us.
     *
     * @return array
     */
    public function register(): array
    {
        return array_keys($this->registeredTokens);
    }
}
