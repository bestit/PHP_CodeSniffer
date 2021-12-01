<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\Sniffs\AbstractSniff;
use function array_merge;
use const T_ASPERAND;
use const T_BOOLEAN_NOT;
use const T_INSTANCEOF;
use const T_OBJECT_OPERATOR;
use const T_STRING;
use const T_VARIABLE;

/**
 * You MUST provide parentheses around your negative instanceof check.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Comparisons
 */
class ParasOfNegativeInstanceOfSniff extends AbstractSniff
{
    /**
     * The error code.
     *
     * @var string
     */
    public const CODE_MISSING_PARAS_AROUND_NEG_INSTANCE_OF = 'ParasAroundNegativeInstanceOfMissing';

    /**
     * Error message.
     *
     * @var string
     */
    private const MESSAGE_MISSING_PARAS_AROUND_NEG_INSTANCE_OF = 'You MUST provide parentheses around your negative ' .
        'instanceof check.';

    /**
     * Caches the position of the boolean not if there is any.
     *
     * @var int|null
     */
    private ?int $notPosition = null;

    /**
     * Adds paras around the negative instance of check.
     *
     * @param CodeWarning $exception
     *
     * @return void
     */
    private function addParasAroundInstanceOf(CodeWarning $exception): void
    {
        $startPos = (int) $this->notPosition + 1;
        $lastPos = (int) TokenHelper::findNextEffective($this->getFile(), $exception->getStackPosition() + 1);

        $oldContent = '';

        $fixer = $this->getFile()->fixer;

        $fixer->beginChangeset();

        for ($fetchStart = $startPos; $fetchStart <= $lastPos; ++$fetchStart) {
            $oldContent .= $this->tokens[$fetchStart]['content'];
            $fixer->replaceToken($fetchStart, '');
        }

        $fixer->replaceToken($this->stackPos, '(' . $oldContent . ')');
        $fixer->endChangeset();
    }

    /**
     * Throws an error if the negative instance of is not done correctly.
     *
     * @throws CodeError
     *
     * @return void
     */
    private function escalateNegativeInstanceOf(): void
    {
        $relevantTokenPos = $this->getPositionOfTokenBeforeCheck();

        if ($this->isBooleanNot($relevantTokenPos)) {
            $this->notPosition = $relevantTokenPos;

            $error = new CodeError(
                static::CODE_MISSING_PARAS_AROUND_NEG_INSTANCE_OF,
                self::MESSAGE_MISSING_PARAS_AROUND_NEG_INSTANCE_OF,
                $this->getStackPos(),
            );

            $error->isFixable(true);

            throw $error;
        }
    }

    /**
     * Fixes the problem and adds paras.
     *
     * @param CodeWarning $exception
     *
     * @return void
     */
    protected function fixDefaultProblem(CodeWarning $exception): void
    {
        if ($this->notPosition) {
            $this->addParasAroundInstanceOf($exception);
        }
    }

    /**
     * Gets the position of the token we need to check.
     *
     * @return int
     */
    private function getPositionOfTokenBeforeCheck(): int
    {
        $file = $this->getFile();
        $stackPosition = $this->getStackPos();

        return TokenHelper::findPreviousExcluding(
            $file,
            // And skip object property accesses
            array_merge(TokenHelper::$ineffectiveTokenCodes, [T_ASPERAND, T_OBJECT_OPERATOR, T_STRING, T_VARIABLE]),
            $stackPosition - 1,
        );
    }

    /**
     * Is the position the forbidden token?
     *
     * @param int $relevantTokenPos
     *
     * @return bool
     */
    private function isBooleanNot(int $relevantTokenPos): bool
    {
        return $this->tokens[$relevantTokenPos]['code'] === T_BOOLEAN_NOT;
    }

    /**
     * Checks if the negative instance of is not encapsulated with paras.
     *
     * @throws CodeError
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this->escalateNegativeInstanceOf();
    }

    /**
     * Registers on the instance of.
     *
     * @return array
     */
    public function register(): array
    {
        return [T_INSTANCEOF];
    }
}
