<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_FUNCTION;
use const T_OPEN_PARENTHESIS;

/**
 * Test TrailingCommaInCallSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class TrailingCommaInCallSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new TrailingCommaInCallSniff();
    }

    /**
     * Checks the required constants.
     *
     * @return array
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_MISSING_TRAILING_COMMA' => ['CODE_MISSING_TRAILING_COMMA', 'MissingTrailingComma'],
        ];
    }

    /**
     * Checks the registered tokens.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_OPEN_PARENTHESIS,
        ];
    }
}
