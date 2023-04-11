<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing;

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

class PropertySpacingSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestTokenRegistrationTrait;
    use TestRequiredConstantsTrait;

    protected function getExpectedTokens(): array
    {
        return [
            T_VAR,
            T_PUBLIC,
            T_PROTECTED,
            T_PRIVATE,
            T_READONLY,
            T_STATIC,
        ];
    }

    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY' => [
                'CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY',
                'IncorrectCountOfBlankLinesAfterProperty',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new PropertySpacingSniff();
    }
}
