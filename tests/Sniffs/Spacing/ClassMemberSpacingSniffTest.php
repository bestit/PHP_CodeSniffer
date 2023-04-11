<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_ENUM;
use const T_INTERFACE;
use const T_TRAIT;

class ClassMemberSpacingSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestTokenRegistrationTrait;
    use TestRequiredConstantsTrait;

    protected function getExpectedTokens(): array
    {
        return [
            T_CLASS,
            T_TRAIT,
            T_INTERFACE,
            T_ENUM,
            T_ANON_CLASS,
        ];
    }

    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS' => [
                'CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS',
                'IncorrectCountOfBlankLinesBetweenMembers',
            ],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new ClassMemberSpacingSniff();
    }
}
