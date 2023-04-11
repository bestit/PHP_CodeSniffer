<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_READONLY;
use const T_STATIC;
use const T_VAR;

class PropertyTypeHintSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    protected function getExpectedTokens(): array
    {
        return [
            T_VAR,
            T_PUBLIC,
            T_PROTECTED,
            T_PRIVATE,
            T_STATIC,
        ];
    }

    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_MISSING_ANY_TYPE_HINT' => ['CODE_MISSING_ANY_TYPE_HINT', 'MissingAnyTypeHint'],
            'CODE_MISSING_NATIVE_TYPE_HINT' => ['CODE_MISSING_NATIVE_TYPE_HINT', 'MissingNativeTypeHint'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new PropertyTypeHintSniff();
    }
}
