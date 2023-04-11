<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_ARRAY;
use const T_OPEN_SHORT_ARRAY;

class TrailingArrayCommaSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    protected function getExpectedTokens(): array
    {
        return [
            T_ARRAY,
            T_OPEN_SHORT_ARRAY,
        ];
    }

    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_MISSING_TRAILING_COMMA' => ['CODE_MISSING_TRAILING_COMMA', 'MissingTrailingComma'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new TrailingArrayCommaSniff();
    }
}
