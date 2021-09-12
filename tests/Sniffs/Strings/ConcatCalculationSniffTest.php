<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Strings;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_STRING_CONCAT;

/**
 * Test ConcatCalculationSniff.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Strings
 */
class ConcatCalculationSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Checks the registration of the sniff.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [T_STRING_CONCAT];
    }

    /**
     * Checks the required constants.
     *
     * @return iterable
     */
    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_CALCULATION_WITHOUT_BRACKETS' => [
                'CODE_CALCULATION_WITHOUT_BRACKETS',
                'CalculationWithoutBrackets',
            ],
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

        $this->fixture = new ConcatCalculationSniff();
    }
}
