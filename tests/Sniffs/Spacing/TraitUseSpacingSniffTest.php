<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use Test_Enum;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_ENUM;
use const T_INTERFACE;
use const T_TRAIT;

class TraitUseSpacingSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    protected function getExpectedTokens(): array
    {
        return [
            T_CLASS => T_CLASS,
            T_ANON_CLASS => T_ANON_CLASS,
            T_INTERFACE => T_INTERFACE,
            T_TRAIT => T_TRAIT,
            T_ENUM => T_ENUM,
        ];
    }

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new TraitUseSpacingSniff();
    }
}
