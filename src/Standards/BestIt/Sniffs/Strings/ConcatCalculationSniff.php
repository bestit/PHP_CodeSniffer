<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Strings;

use BestIt\CodeSniffer\CodeError;
use BestIt\Sniffs\AbstractSniff;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_DIVIDE;
use const T_MINUS;
use const T_MULTIPLY;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
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
     * @throws CodeError
     *
     * @return void
     */
    private function checkForCalculationAfterToken(): void
    {
        $nextDelimiterPos = $this->file->findNext(
            [
                T_CLOSE_CURLY_BRACKET,
                T_CLOSE_PARENTHESIS,
                T_CLOSE_SHORT_ARRAY,
                T_OPEN_PARENTHESIS,
                T_SEMICOLON,
                T_STRING_CONCAT,
            ],
            $this->getStackPos() + 1,
        );

        $nextMathPos = $this->file->findNext(
            [T_DIVIDE, T_MINUS, T_MULTIPLY, T_PLUS],
            $this->getStackPos() + 1,
            (int) $nextDelimiterPos,
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
     * @throws CodeError
     *
     * @return $this
     */
    private function checkForCalculationBeforeToken(): self
    {
        $prevDelimiterPos = $this->file->findPrevious(
            [
                T_CLOSE_PARENTHESIS,
                T_OPEN_PARENTHESIS,
                T_OPEN_CURLY_BRACKET,
                T_OPEN_PARENTHESIS,
                T_OPEN_SHORT_ARRAY,
                T_SEMICOLON,
                T_STRING_CONCAT,
            ],
            $this->getStackPos() - 1,
        );

        $prevMathPos = $this->file->findPrevious(
            [T_DIVIDE, T_MINUS, T_MULTIPLY, T_PLUS],
            $this->getStackPos() - 1,
            (int) $prevDelimiterPos,
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
     * @param int $mathPos
     *
     * @return bool
     */
    private function isCheckAlreadyDone(int $mathPos): bool
    {
        $checkMarker = $this->file->getFilename() . $mathPos;
        $isDone = @self::$alreadyDoneChecks[$checkMarker];

        self::$alreadyDoneChecks[$checkMarker] = true;

        return (bool) $isDone;
    }

    /**
     * Processes the token.
     *
     * @throws CodeError
     *
     * @return void
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
