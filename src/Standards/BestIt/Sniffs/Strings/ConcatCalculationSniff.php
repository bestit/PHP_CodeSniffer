<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Strings;

use BestIt\CodeSniffer\CodeError;
use BestIt\Sniffs\AbstractSniff;
use const T_CLOSE_PARENTHESIS;
use const T_MINUS;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_PLUS;
use const T_SEMICOLON;
use const T_STRING_CONCAT;

/**
 * You MUST encapsulate your calculation with brackets.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Strings
 */
class ConcatCalculationSniff extends AbstractSniff
{
    /**
     * You MUST encapsulate your calculation with brackets.
     *
     * @var string
     */
    public const CODE_CALCULATION_WITHOUT_BRACKETS = 'CalculationWithoutBrackets';

    /**
     * The error message for the error.
     *
     * @var string
     */
    private const MESSAGE_CALCULATION_WITHOUT_BRACKETS = 'You should encapsulate your calculation with brackets.';

    /**
     * Caching of the checks, already done.
     *
     * @var array
     */
    private static array $alreadyDoneChecks = [];

    /**
     * Checks for a possible wrong calculation after the token.
     *
     * @return void
     * @throws CodeError
     *
     */
    protected function checkForCalculationAfterToken(): void
    {
        $nextClosingPos = $this->file->findNext(
            [T_OPEN_PARENTHESIS, T_SEMICOLON, T_STRING_CONCAT],
            $this->getStackPos() + 1,
        );

        $nextMathPos = $this->file->findNext(
            [T_MINUS, T_PLUS],
            $this->getStackPos() + 1,
            (int) $nextClosingPos,
        );

        if ($nextMathPos !== false) {
            throw new CodeError(
                static::CODE_CALCULATION_WITHOUT_BRACKETS,
                self::MESSAGE_CALCULATION_WITHOUT_BRACKETS,
                $nextMathPos,
            );
        }
    }

    /**
     * Checks for a possible wrong calculation before the token.
     *
     * @return $this
     * @throws CodeError
     *
     */
    protected function checkForCalculationBeforeToken(): self
    {
        $prevClosingPos = $this->file->findPrevious(
            [T_CLOSE_PARENTHESIS, T_OPEN_CURLY_BRACKET, T_SEMICOLON],
            $this->getStackPos() + 1,
        );

        $prevMathPos = $this->file->findPrevious(
            [T_MINUS, T_PLUS],
            $this->getStackPos() + 1,
            (int) $prevClosingPos,
        );

        if (($prevMathPos !== false) && !$this->isCheckAlreadyDone($prevMathPos)) {
            throw new CodeError(
                static::CODE_CALCULATION_WITHOUT_BRACKETS,
                self::MESSAGE_CALCULATION_WITHOUT_BRACKETS,
                $prevMathPos,
            );
        }

        return $this;
    }

    /**
     * Checks if the check for the given position in the actual file was already done.
     *
     * @param int $prevMathPos
     * @param bool $withSave
     *
     * @return bool
     */
    private function isCheckAlreadyDone(int $prevMathPos, bool $withSave = true): bool
    {
        $checkMarker = $this->file->getFilename() . $prevMathPos;
        $isDone = @self::$alreadyDoneChecks[$checkMarker];

        if ($withSave) {
            self::$alreadyDoneChecks[$checkMarker] = true;
        }

        return (bool) $isDone;
    }

    /**
     * Processes the token.
     *
     * @return void
     * @throws CodeError
     *
     */
    protected function processToken(): void
    {
        // Who does 1 + 2 . 1 + 2 in reality? So I skipped it for now!
        $this
            ->checkForCalculationBeforeToken()
            ->checkForCalculationAfterToken();
    }

    /**
     * Registers the tokens on string concatinations.
     *
     * @return array
     */
    public function register(): array
    {
        return [T_STRING_CONCAT];
    }
}
