<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\CodeSniffer\File as FileDecorator;
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
     * @var boolean
     */
    public $error = false;

    /**
     * The used file.
     *
     * @var FileDecorator|void
     */
    protected $file;

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the function should just not be used.
     *
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
     * @var int|void
     */
    protected $stackPos;

    /**
     * Type-safe getter for the file.
     *
     * @return FileDecorator
     */
    protected function getFile(): FileDecorator
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
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $this->file = new FileDecorator($phpcsFile);
        $this->stackPos = $stackPtr;

        if (
            !$this->isSniffSuppressed(static::CODE_DISCOURAGED_WITHOUT_ALTERNATIVE) &&
            !$this->isSniffSuppressed(static::CODE_DISCOURAGED_WITH_ALTERNATIVE)
        ) {
            parent::process($phpcsFile, $stackPtr);
        }
    }
}
