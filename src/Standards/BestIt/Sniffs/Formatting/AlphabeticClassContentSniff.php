<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\CodeSniffer\Helper\PropertyHelper;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\ClassRegistrationTrait;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_values;
use function natsort;
use const T_CONST;
use const T_FUNCTION;
use const T_STRING;
use const T_VARIABLE;

/**
 * Registers a warning, if the constants, properties or methods are not sorted alphabetically.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class AlphabeticClassContentSniff extends AbstractSniff
{
    use  ClassRegistrationTrait;

    /**
     * Error code for the sorting.
     *
     * @var string
     */
    public const CODE_SORT_ALPHABETICALLY = 'SortAlphabetically';

    /**
     * The message for the wrong sorting.
     *
     * @var string
     */
    private const MESSAGE_SORT_ALPHABETICALLY = 'Please sort you contents alphabetically.';

    /**
     * Checks the sorting of both arrays and registered warnings, if a token is not on the correct position.
     *
     * @param array $foundContentsOrg The original contents with their position.
     * @param array $foundContentsSorted The sorted contents without their position as array key.
     *
     * @return void
     */
    private function checkAndRegisterSortingProblems(array $foundContentsOrg, array $foundContentsSorted): void
    {
        $checkIndex = 0;

        foreach ($foundContentsOrg as $foundContentPos => $foundContent) {
            if ($foundContentsSorted[$checkIndex++] !== $foundContent) {
                $this->file->getBaseFile()->addWarning(
                    self::MESSAGE_SORT_ALPHABETICALLY,
                    $foundContentPos,
                    self::CODE_SORT_ALPHABETICALLY
                );
            }
        }
    }

    /**
     * Loads every content for the token type and checks their sorting.
     *
     * @param int $token
     *
     * @return void
     */
    private function checkAndRegisterSortingProblemsOfTypes(int $token): void
    {
        $foundContentsOrg = $this->getContentsOfTokenType($token);

        $foundContentsSorted = $this->sortTokensWithoutPos($foundContentsOrg);

        $this->checkAndRegisterSortingProblems($foundContentsOrg, $foundContentsSorted);
    }

    /**
     * Returns the contents of the token type.
     *
     * @param int $token The contents with their position as array key.
     *
     * @return array
     */
    private function getContentsOfTokenType(int $token): array
    {
        $helper = new PropertyHelper($this->file);
        $tokenPoss = TokenHelper::findNextAll(
            $this->file->getBaseFile(),
            [$token],
            $this->stackPos + 1,
            $this->token['scope_closer']
        );

        $foundContentsOrg = [];

        foreach ($tokenPoss as $tokenPos) {
            $tokenContentPos = $tokenPos;

            if (($token === T_VARIABLE) && (!$helper->isProperty($tokenPos))) {
                continue;
            }

            if ($token !== T_VARIABLE) {
                $tokenContentPos = $this->file->findNext([T_STRING], $tokenPos);
            }

            $foundContentsOrg[$tokenContentPos] = $this->tokens[$tokenContentPos]['content'];
        }

        return $foundContentsOrg;
    }

    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $tokenTypes = [T_CONST, T_FUNCTION, T_VARIABLE];

        foreach ($tokenTypes as $tokenType) {
            $this->checkAndRegisterSortingProblemsOfTypes($tokenType);
        }
    }

    /**
     * Sorts the tokens and returns them without their position as array keys.
     *
     * @param array $foundContentsOrg
     *
     * @return array
     */
    private function sortTokensWithoutPos(array $foundContentsOrg): array
    {
        $foundContentsSorted = $foundContentsOrg;

        natsort($foundContentsSorted);

        return array_values($foundContentsSorted); // "remove" indices

        return $foundContentsSorted;
    }
}
