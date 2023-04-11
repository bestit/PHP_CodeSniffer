<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_ENUM;
use const T_INTERFACE;
use const T_TRAIT;

class TraitUseDeclarationSniffTest extends SniffTestCase
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
            'CODE_MULTIPLE_TRAITS_PER_DECLARATION' =>
                ['CODE_MULTIPLE_TRAITS_PER_DECLARATION', 'MultipleTraitsPerDeclaration'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new TraitUseDeclarationSniff();
    }
}
