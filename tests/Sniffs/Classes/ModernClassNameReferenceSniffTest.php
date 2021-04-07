<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Classes;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_CLASS_C;
use const T_NAME_FULLY_QUALIFIED;
use const T_NAME_QUALIFIED;
use const T_NAME_RELATIVE;
use const T_STRING;

class ModernClassNameReferenceSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_CLASS_NAME_REFERENCED_VIA_FUNCTION_CALL' => [
                'CODE_CLASS_NAME_REFERENCED_VIA_FUNCTION_CALL',
                'ClassNameReferencedViaFunctionCall',
            ],
            'CODE_CLASS_NAME_REFERENCED_VIA_MAGIC_CONSTANT' => [
                'CODE_CLASS_NAME_REFERENCED_VIA_MAGIC_CONSTANT',
                'ClassNameReferencedViaMagicConstant',
            ],
        ];
    }

    protected function getExpectedTokens(): array
    {
        return [T_STRING, T_NAME_FULLY_QUALIFIED, T_NAME_QUALIFIED, T_NAME_RELATIVE, T_CLASS_C];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new ModernClassNameReferenceSniff();
    }
}
