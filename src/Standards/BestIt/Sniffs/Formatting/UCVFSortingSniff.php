<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\CodeSniffer\Helper\PropertyHelper;
use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\CodeSniffer\Helper\UseStatementHelper;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\ClassRegistrationTrait;
use function array_combine;
use function array_filter;
use function array_keys;
use function array_map;
use function array_search;
use function uasort;
use const ARRAY_FILTER_USE_BOTH;
use const T_CONST;
use const T_FUNCTION;
use const T_USE;
use const T_VARIABLE;

/**
 * Checks the sorting of the contents of a class (T_USE > T_CONST > T_VARIABLE > T_FUNCTION).
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class UCVFSortingSniff extends AbstractSniff
{
    use ClassRegistrationTrait;

    /**
     * You MUST sort the contents of your classes, traits, interface, etc. in the following order: T_USE, T_CONST, T_VARIABLE, T_FUNCTION.
     *
     * @var string
     */
    public const CODE_WRONG_POSITION = 'WrongPosition';

    /**
     * The error message of a structure is at the wrong position.
     *
     * @var string
     */
    private const MESSAGE_WRONG_POSITION = 'Your php structure is at a wrong position. ' .
        'The sorting order is: T_USE, T_CONST, T_VARIABLE, T_FUNCTION. We expect a %s (%s).';

    /**
     * The tokens which are checked in the correct sorting order.
     *
     * @var array
     */
    private array $sortedTokens = [
        T_USE,
        T_CONST,
        T_VARIABLE,
        T_FUNCTION,
    ];

    /**
     * Loads the positons for the tokens of $this->>sortedTokens.
     *
     * @return int[]
     */
    private function loadSubTokenPositions(): array
    {
        return TokenHelper::findNextAll(
            $this->file,
            $this->sortedTokens,
            $this->stackPos,
            $this->token['scope_closer'],
        );
    }

    /**
     * Loads all sub tokens.
     *
     * @return array
     */
    private function loadSubTokens(): array
    {
        $subTokens = $this->loadTokensForPositions($this->loadSubTokenPositions());

        return $this->removeUnwantedTokens($subTokens);
    }

    /**
     * Loads the tokens for the positions.
     *
     * @param array $subTokenPoss
     *
     * @return array
     */
    private function loadTokensForPositions(array $subTokenPoss): array
    {
        $subTokens = array_map(function (int $position): array {
            return $this->tokens[$position];
        }, $subTokenPoss);

        return array_combine($subTokenPoss, $subTokens);
    }

    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $subTokens = $this->loadSubTokens();
        $sortedTokens = $this->sortTokens($subTokens);

        $this->validateSorting($subTokens, $sortedTokens);
    }

    /**
     * Removes inline vars and uses for anon-functions.
     *
     * @param array $subTokens
     *
     * @return array
     */
    private function removeUnwantedTokens(array $subTokens): array
    {
        return array_filter($subTokens, function (array $subToken, int $tokenPos): bool {
            switch ($subToken['code']) {
                case T_VARIABLE:
                    $return = (new PropertyHelper($this->file))->isProperty($tokenPos);
                    break;

                case T_USE:
                    $return = UseStatementHelper::isTraitUse($this->file, $tokenPos);
                    break;

                default:
                    $return = true;
            }

            return $return;
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Sorts the tokens as required by $this->sortingTokens.
     *
     * @param array $subTokens
     *
     * @return array
     */
    private function sortTokens(array $subTokens): array
    {
        uasort($subTokens, function (array $leftToken, array $rightToken): int {
            // Don't change the structure by default.
            $return = $leftToken['line'] <=> $rightToken['line'];

            // Sort by type
            if ($leftToken['code'] != $rightToken['code']) {
                $leftIndex = array_search($leftToken['code'], $this->sortedTokens);
                $rightIndex = array_search($rightToken['code'], $this->sortedTokens);

                $return = $leftIndex <=> $rightIndex;
            }

            return $return;
        });

        return $subTokens;
    }

    /**
     * Validates the sorting of the tokens and registers an error if wrong sorted.
     *
     * @param array $originalTokens
     * @param array $sortedTokens
     *
     * @return void
     */
    private function validateSorting(array $originalTokens, array $sortedTokens): void
    {
        $sortedPositions = array_keys($sortedTokens);
        $sortedIndex = 0;

        foreach ($originalTokens as $originalPosition => $originalToken) {
            $sortedPosition = $sortedPositions[$sortedIndex++];
            $sortedToken = $sortedTokens[$sortedPosition];

            // We don't need an error if the "type block" is the same, so check the code additionally.
            if (($sortedPosition !== $originalPosition) && ($originalToken['code'] !== $sortedToken['code'])) {
                $this->file->addError(
                    self::MESSAGE_WRONG_POSITION,
                    $originalPosition,
                    static::CODE_WRONG_POSITION,
                    [
                        $sortedToken['type'],
                        $sortedToken['content'],
                    ],
                );
            }
        }
    }
}
