<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_INSTANCEOF;

/**
 * Tests ParasOfNegativeInstanceOfSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Comparisons
 */
class ParasOfNegativeInstanceOfSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Checks the reqistration.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [T_INSTANCEOF];
    }

    /**
     * Checks the required constants.
     *
     * @return iterable
     */
    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_MISSING_PARAS_AROUND_NEG_INSTANCE_OF' => [
                'CODE_MISSING_PARAS_AROUND_NEG_INSTANCE_OF',
                'ParasAroundNegativeInstanceOfMissing',
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

        $this->fixture = new ParasOfNegativeInstanceOfSniff();
    }
}
