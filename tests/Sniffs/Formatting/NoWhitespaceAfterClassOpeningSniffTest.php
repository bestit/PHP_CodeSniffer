<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_ENUM;

class NoWhitespaceAfterClassOpeningSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestTokenRegistrationTrait;
    use TestRequiredConstantsTrait;

    protected function getExpectedTokens(): array
    {
        return [
            T_CLASS,
            T_ANON_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_ENUM,
        ];
    }

    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE' =>
                ['CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE', 'IncorrectEmptyLinesAfterOpeningBrace'],
            'CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE' =>
                ['CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE', 'IncorrectEmptyLinesBeforeClosingBrace'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new NoWhitespaceAfterClassOpeningSniff();
    }
}
