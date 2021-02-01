<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_CLOSURE;
use const T_FN;
use const T_FUNCTION;

class SuggestExplicitReturnTypeSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    protected function getExpectedTokens(): array
    {
        return [
            T_FUNCTION,
            T_CLOSURE,
            T_FN,
        ];
    }

    public function getRequiredConstantAsserts(): iterable
    {
        return ['CODE_MIXED_TYPE' => ['CODE_MIXED_TYPE', 'MixedType']];
    }

    protected function setUp(): void
    {
        $this->testedObject = new SuggestExplicitReturnTypeSniff();
    }
}
