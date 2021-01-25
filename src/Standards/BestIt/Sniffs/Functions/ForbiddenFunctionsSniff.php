<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\SuppressingTrait;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff as BaseSniff;

/**
 * You SHOULD not use eval, die, sizeof, delete ...
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class ForbiddenFunctionsSniff extends BaseSniff
{
    use SuppressingTrait;

    /**
     * You SHOULD not use eval.
     */
    public const CODE_DISCOURAGED_WITHOUT_ALTERNATIVE = 'Discouraged';

    /**
     * You SHOULD not use alias but the original function names.
     */
    public const CODE_DISCOURAGED_WITH_ALTERNATIVE = 'DiscouragedWithAlternative';

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var boolean
     */
    public $error = false;

    /**
     * The used file.
     *
     * @var File|null
     */
    protected File|null $file = null;

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the function should just not be used.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var array<string, string|null>
     */
    public $forbiddenFunctions = [
        'eval' => null,
        'die' => 'exit',
        'sizeof' => 'count',
        'delete' => 'unset',
    ];

    /**
     * Position of the listened token.
     *
     * @var int|null
     */
    protected ?int $stackPos = null;

    /**
     * Type-safe getter for the file.
     *
     * @return File
     */
    protected function getFile(): File
    {
        return $this->file;
    }

    /**
     * Type-safe getter for the stack position.
     *
     * @return int
     */
    protected function getStackPos(): int
    {
        return $this->stackPos;
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     *
     * @param File $file The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $file, $stackPtr): void
    {
        $this->file = $file;
        $this->stackPos = $stackPtr;

        if (
            !$this->isSniffSuppressed(static::CODE_DISCOURAGED_WITHOUT_ALTERNATIVE) &&
            !$this->isSniffSuppressed(static::CODE_DISCOURAGED_WITH_ALTERNATIVE)
        ) {
            parent::process($file, $stackPtr);
        }
    }
}
