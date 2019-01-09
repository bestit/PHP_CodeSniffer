<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\CodeSniffer\Helper\ClassHelper;
use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\ClassRegistrationTrait;
use function substr_count;
use const T_CLOSE_CURLY_BRACKET;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_WHITESPACE;

/**
 * Checks the newlines between the trait uses.
 *
 * This is a refactores copy of the slevomat code sniff.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class TraitUseSpacingSniff extends AbstractSniff
{
    use ClassRegistrationTrait;

    /**
     * You MUST not provide additional lines after your last rait usage.
     */
    public const CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE = 'IncorrectLinesCountAfterLastUse';

    /**
     * You MUST not provide additional new lines before your first trait use.
     */
    public const CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE = 'IncorrectLinesCountBeforeFirstUse';

    /**
     * You MUST not provide additional new lines between trait usages.
     */
    public const CODE_INCORRECT_LINES_COUNT_BETWEEN_USES = 'IncorrectLinesCountBetweenUses';

    /**
     * How many lines after the last use.
     */
    private const LINES_AFTER_LAST_USE = 1;

    /**
     * How many lines after the last use.
     */
    private const LINES_AFTER_LAST_USE_WHEN_LAST_IN_CLASS = 0;

    /**
     * How many use before the first one.
     */
    private const LINES_BEFORE_FIRST_USE = 0;

    /**
     * How many lines between the uses.
     */
    private const LINES_BETWEEN_USES = 0;

    /**
     * The message to the user for the error before usages.
     */
    private const MESSAGE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE =
        'Expected %d lines before first use statement, found %d.';

    /**
     * The message to the user for the error after the last usage.
     */
    private const MESSAGE_INCORRECT_LINES_COUNT_AFTER_LAST_USE =
        'Expected %d lines after last use statement, found %d.';

    /**
     * The message to the user for the error between uses.
     */
    private const MESSAGE_INCORRECT_LINES_COUNT_BETWEEN_USES =
        'Expected %d lines between same types of use statement, found %d.';

    /**
     * The use declarations positions of this "class".
     *
     * @var array
     */
    private $uses;

    /**
     * Returns false if there are no uses.
     *
     * @return bool
     */
    protected function areRequirementsMet(): bool
    {
        return (bool) $this->uses = ClassHelper::getTraitUsePointers($this->getFile(), $this->getStackPos());
    }

    /**
     * Checks the line after the last use and registers an error if needed.
     *
     * @param int $lastUsePos
     *
     * @return void
     */
    private function checkLinesAfterLastUse(int $lastUsePos): void
    {
        $lastUseEndPos = $this->getLastUseEndPos($lastUsePos);

        list($realLinesAfterUse, $whitespaceEnd) = $this->getRealLinesAfterLastUse($lastUseEndPos);

        $requiredLinesAfter = $this->isEndOfClass($lastUseEndPos)
            ? self::LINES_AFTER_LAST_USE_WHEN_LAST_IN_CLASS
            : self::LINES_AFTER_LAST_USE;

        if ($realLinesAfterUse !== $requiredLinesAfter) {
            $fix = $this->getFile()->addFixableError(
                self::MESSAGE_INCORRECT_LINES_COUNT_AFTER_LAST_USE,
                $lastUsePos,
                self::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE,
                [
                    $requiredLinesAfter,
                    $realLinesAfterUse
                ]
            );

            if ($fix) {
                $this->fixLineAfterLastUse($lastUseEndPos, $whitespaceEnd, $requiredLinesAfter);
            }
        }
    }

    /**
     * Checks the lines before the first usage and registers an error if needed.
     *
     * @param int $firstUsePos
     *
     * @return void
     */
    private function checkLinesBeforeFirstUse(int $firstUsePos): void
    {
        $posBeforeFirstUse = TokenHelper::findPreviousExcluding($this->getFile(), T_WHITESPACE, $firstUsePos - 1);
        $realLinesBeforeUse = $this->getRealLinesBeforeFirstUse($firstUsePos, $posBeforeFirstUse);

        if ($realLinesBeforeUse !== self::LINES_BEFORE_FIRST_USE) {
            $fix = $this->getFile()->addFixableError(
                self::MESSAGE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE,
                $firstUsePos,
                self::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE,
                [
                    self::LINES_BEFORE_FIRST_USE,
                    $realLinesBeforeUse
                ]
            );

            if ($fix) {
                $this->fixLinesBeforeFirstUse($firstUsePos, $posBeforeFirstUse, self::LINES_BEFORE_FIRST_USE);
            }
        }
    }

    /**
     * Checks the lines between uses and registers an erro rif needed.
     *
     * @return void
     */
    private function checkLinesBetweenUses(): void
    {
        $file = $this->getFile();
        $previousUsePos = null;

        foreach ($this->uses as $usePos) {
            if ($previousUsePos === null) {
                $previousUsePos = $usePos;
                continue;
            }

            $posBeforeUse = TokenHelper::findPreviousEffective($file, $usePos - 1);
            $previousUseEndPos = TokenHelper::findNextLocal(
                $file,
                [T_SEMICOLON, T_OPEN_CURLY_BRACKET],
                $previousUsePos + 1
            );

            $realLinesBetweenUse = $this->getRealLinesBetweenUses(
                $previousUseEndPos,
                $usePos
            );

            $previousUsePos = $usePos;

            if ($realLinesBetweenUse !== self::LINES_BETWEEN_USES) {
                $errorParameters = [
                    self::MESSAGE_INCORRECT_LINES_COUNT_BETWEEN_USES,
                    $usePos,
                    self::CODE_INCORRECT_LINES_COUNT_BETWEEN_USES,
                    [
                        self::LINES_BETWEEN_USES,
                        $realLinesBetweenUse
                    ]
                ];

                if ($previousUseEndPos !== $posBeforeUse) {
                    $file->addError(...$errorParameters);
                } else {
                    $fix = $file->addFixableError(...$errorParameters);

                    if ($fix) {
                        $this->fixLinesBetweenUses($usePos, $previousUseEndPos, self::LINES_BETWEEN_USES);
                    }
                }
            }
        }
    }

    /**
     * Fixes the lines which are allowed after the last use.
     *
     * @param int $lastUseEndPos
     * @param int $whitespaceEnd
     * @param int $requiredLinesAfter
     *
     * @return void
     */
    private function fixLineAfterLastUse(
        int $lastUseEndPos,
        int $whitespaceEnd,
        int $requiredLinesAfter
    ): void {
        $file = $this->getFile();

        $file->fixer->beginChangeset();

        for ($i = $lastUseEndPos + 1; $i <= $whitespaceEnd; $i++) {
            $file->fixer->replaceToken($i, '');
        }

        for ($i = 0; $i <= $requiredLinesAfter; $i++) {
            $file->fixer->addNewline($lastUseEndPos);
        }

        $file->fixer->endChangeset();
    }

    /**
     * Fixes the lines before the first use.
     *
     * @param int $firstUsePos
     * @param int $posBeforeFirstUse
     *
     * @return void
     */
    private function fixLinesBeforeFirstUse(
        int $firstUsePos,
        int $posBeforeFirstUse
    ): void {
        $file = $this->getFile();
        $file->fixer->beginChangeset();

        $posBeforeIndentation = TokenHelper::findPreviousContent(
            $file,
            T_WHITESPACE,
            $file->eolChar,
            $firstUsePos,
            $posBeforeFirstUse
        );

        if ($posBeforeIndentation !== null) {
            for ($i = $posBeforeFirstUse + 1; $i <= $posBeforeIndentation; $i++) {
                $file->fixer->replaceToken($i, '');
            }
        }
        for ($i = 0; $i <= self::LINES_BEFORE_FIRST_USE; $i++) {
            $file->fixer->addNewline($posBeforeFirstUse);
        }

        $file->fixer->endChangeset();
    }

    /**
     * Fixes the lines between the uses.
     *
     * @param int $usePos
     * @param int $previousUseEndPos
     *
     * @return void
     */
    private function fixLinesBetweenUses(int $usePos, int $previousUseEndPos): void
    {
        $file = $this->getFile();

        $posBeforeIndentation = TokenHelper::findPreviousContent(
            $file,
            T_WHITESPACE,
            $file->eolChar,
            $usePos,
            $previousUseEndPos
        );

        $file->fixer->beginChangeset();
        if ($posBeforeIndentation !== null) {
            for ($i = $previousUseEndPos + 1; $i <= $posBeforeIndentation; $i++) {
                $file->fixer->replaceToken($i, '');
            }
        }
        for ($i = 0; $i <= self::LINES_BETWEEN_USES; $i++) {
            $file->fixer->addNewline($previousUseEndPos);
        }
        $file->fixer->endChangeset();
    }

    /**
     * Gets the position on which the last use ends.
     *
     * @param int $lastUsePos
     *
     * @return int
     */
    private function getLastUseEndPos(int $lastUsePos): int
    {
        $file = $this->getFile();
        $tokens = $file->getTokens();

        $lastUseEndPos = TokenHelper::findNextLocal(
            $file,
            [T_SEMICOLON, T_OPEN_CURLY_BRACKET],
            $lastUsePos + 1
        );

        if ($tokens[$lastUseEndPos]['code'] === T_OPEN_CURLY_BRACKET) {
            $lastUseEndPos = $tokens[$lastUseEndPos]['bracket_closer'];
        }

        return $lastUseEndPos;
    }

    /**
     * Gets the real lines after the last use.
     *
     * @param int $lastUseEndPos
     *
     * @return array The first element is the line count, and the second element is when the whitespace ends.
     */
    private function getRealLinesAfterLastUse(int $lastUseEndPos): array
    {
        $file = $this->getFile();
        $tokens = $file->getTokens();
        $whitespaceEnd = TokenHelper::findNextExcluding($file, T_WHITESPACE, $lastUseEndPos + 1) - 1;

        if ($lastUseEndPos !== $whitespaceEnd && $tokens[$whitespaceEnd]['content'] !== $file->eolChar) {
            $lastEolPos = TokenHelper::findPreviousContent(
                $file,
                T_WHITESPACE,
                $file->eolChar,
                $whitespaceEnd - 1,
                $lastUseEndPos
            );
            $whitespaceEnd = $lastEolPos ?? $lastUseEndPos;
        }

        $whitespaceAfterLastUse = TokenHelper::getContent($file, $lastUseEndPos + 1, $whitespaceEnd);

        $realLinesAfterUse = substr_count($whitespaceAfterLastUse, $file->eolChar) - 1;

        return [$realLinesAfterUse, $whitespaceEnd];
    }

    /**
     * Returns the real lines before the first use.
     *
     * @param int $firstUsePos
     * @param int $posBeforeFirstUse
     *
     * @return int
     */
    private function getRealLinesBeforeFirstUse(int $firstUsePos, int $posBeforeFirstUse): int
    {
        $file = $this->getFile();
        $whitespaceBeforeFirstUse = '';

        if ($posBeforeFirstUse + 1 !== $firstUsePos) {
            $whitespaceBeforeFirstUse .= TokenHelper::getContent(
                $file,
                $posBeforeFirstUse + 1,
                $firstUsePos - 1
            );
        }

        return substr_count($whitespaceBeforeFirstUse, $file->eolChar) - 1;
    }

    /**
     * Returns the real lines between the uses.
     *
     * @param int $previousUseEndPos
     * @param int $usePos
     *
     * @return int
     */
    private function getRealLinesBetweenUses(int &$previousUseEndPos, int $usePos): int
    {
        $tokens = $this->getFile()->getTokens();

        if ($tokens[$previousUseEndPos]['code'] === T_OPEN_CURLY_BRACKET) {
            $previousUseEndPos = $tokens[$previousUseEndPos]['bracket_closer'];
        }

        return $tokens[$usePos]['line'] - $tokens[$previousUseEndPos]['line'] - 1;
    }

    /**
     * Is the given Position the end of the class.
     *
     * @param int $lastUseEndPos
     *
     * @return bool
     */
    private function isEndOfClass(int $lastUseEndPos): bool
    {
        $file = $this->getFile();
        $tokens = $file->getTokens();

        $posAfterLastUse = TokenHelper::findNextEffective($file, $lastUseEndPos + 1);

        return $tokens[$posAfterLastUse]['code'] === T_CLOSE_CURLY_BRACKET;
    }

    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this->checkLinesBeforeFirstUse($this->uses[0]);
        $this->checkLinesAfterLastUse($this->uses[count($this->uses) - 1]);

        if (count($this->uses) > 1) {
            $this->checkLinesBetweenUses();
        }
    }
}
