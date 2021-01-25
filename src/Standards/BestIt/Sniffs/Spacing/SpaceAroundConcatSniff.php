<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use BestIt\Sniffs\AbstractSniff;
use const T_STRING_CONCAT;
use const T_WHITESPACE;

/**
 * Checks for the space around concat dots and fixes them if they are missing.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Spacing
 */
class SpaceAroundConcatSniff extends AbstractSniff
{
    /**
     * You MUST wrap your concat-dot with a whitespace char.
     *
     * @var string
     */
    public const CODE_MISSING_SPACE_AROUND_CONCAT = 'MissingSpaceAroundConcat';

    /**
     * The message to the user for this error.
     *
     * @var string
     */
    private const MESSAGE_MISSING_SPACE_AROUND_CONCAT = 'Please wrap your concatinations with a whitespace char.';

    /**
     * Is the next token whitespace?
     *
     * @var bool|null
     */
    private ?bool $nextIsWhitespace = null;

    /**
     * Is the prev token whitespace?
     *
     * @var null|bool
     */
    private ?bool $prevIsWhitespace = null;

    /**
     * Adds whitespace around the concats.
     *
     * @param CodeWarning $warning
     *
     * @return void
     */
    protected function fixDefaultProblem(CodeWarning $warning): void
    {
        $newContent = '';

        if (!$this->prevIsWhitespace) {
            $newContent = ' ';
        }

        $newContent .= '.';

        if (!$this->nextIsWhitespace) {
            $newContent .= ' ';
        }

        $fixer = $this->getFile()->fixer;

        $fixer->beginChangeset();
        $fixer->replaceToken($this->stackPos, $newContent);
        $fixer->endChangeset();
    }

    /**
     * Processes the token.
     *
     * @throws CodeError
     *
     * @return void
     *
     */
    protected function processToken(): void
    {
        if (!(($this->nextIsWhitespace) && ($this->prevIsWhitespace))) {
            $error = new CodeError(
                static::CODE_MISSING_SPACE_AROUND_CONCAT,
                self::MESSAGE_MISSING_SPACE_AROUND_CONCAT,
                $this->stackPos,
            );

            $error->isFixable(true);

            throw $error;
        }
    }

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * An example return value for a sniff that wants to listen for whitespace
     * and any comments would be:
     *
     * <code>
     *    return array(
     *            T_WHITESPACE,
     *            T_DOC_COMMENT,
     *            T_COMMENT,
     *           );
     * </code>
     *
     * @return int[]
     */
    public function register(): array
    {
        return [T_STRING_CONCAT];
    }

    /**
     * Sets up the sniff.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $nextToken = $this->tokens[$this->stackPos + 1];
        $prevToken = $this->tokens[$this->stackPos - 1];

        $this->nextIsWhitespace = $nextToken['code'] === T_WHITESPACE;
        $this->prevIsWhitespace = $prevToken['code'] === T_WHITESPACE;
    }
}
