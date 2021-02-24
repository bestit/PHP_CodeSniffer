<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;

/**
 * Tests the Class NoWhitespaceAfterClassOpeningSniff.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class NoWhitespaceAfterClassOpeningSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestTokenRegistrationTrait;
    use TestRequiredConstantsTrait;

    /**
     * Returns the array of registered tokens.
     *
     * @return iterable
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_CLASS,
            T_ANON_CLASS,
            T_INTERFACE,
            T_TRAIT,
        ];
    }

    /**
     * Returns the constants which are required.
     *
     * @return iterable
     */
    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE' =>
                ['CODE_INCORRECT_EMPTY_LINES_AFTER_OPENING_BRACE', 'IncorrectEmptyLinesAfterOpeningBrace'],
            'CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE' =>
                ['CODE_INCORRECT_EMPTY_LINES_BEFORE_CLOSING_BRACE', 'IncorrectEmptyLinesBeforeClosingBrace'],
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

        $this->fixture = new NoWhitespaceAfterClassOpeningSniff();
    }
}
