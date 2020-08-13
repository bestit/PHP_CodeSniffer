<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_INTERFACE;
use const T_TRAIT;

/**
 * Tests TraitUseSpacingSniff.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class TraitUseSpacingSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Returns the tokens which should be checked.
     *
     * @return array Returns the expected token ids.
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_CLASS => T_CLASS,
            T_ANON_CLASS => T_ANON_CLASS,
            T_INTERFACE => T_INTERFACE,
            T_TRAIT => T_TRAIT,
        ];
    }

    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE' => [
                'CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE',
                'IncorrectLinesCountBeforeFirstUse',

            ],
            'CODE_INCORRECT_LINES_COUNT_BETWEEN_USES' => [
                'CODE_INCORRECT_LINES_COUNT_BETWEEN_USES',
                'IncorrectLinesCountBetweenUses',
            ],
            'CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE' => [
                'CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE',
                'IncorrectLinesCountAfterLastUse',
            ],
        ];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new TraitUseSpacingSniff();
    }
}
