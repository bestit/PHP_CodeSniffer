<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_OPEN_SHORT_ARRAY;

/**
 * Test TrailingArrayCommaSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class TrailingArrayCommaSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * We register on arrays.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_OPEN_SHORT_ARRAY,
        ];
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
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new TrailingArrayCommaSniff();
    }
}
