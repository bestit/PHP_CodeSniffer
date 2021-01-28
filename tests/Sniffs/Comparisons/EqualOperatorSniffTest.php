<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_IS_NOT_EQUAL;

/**
 * Class EqualOperatorSniffTest.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\Comparisons
 */
class EqualOperatorSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Get the expected tokens.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [T_IS_EQUAL, T_IS_NOT_EQUAL];
    }

    /**
     * Get required constants.
     *
     * @return array
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_EQUAL_OPERATOR_FOUND' => ['CODE_EQUAL_OPERATOR_FOUND', 'EqualOperatorFound'],
        ];
    }

    /**
     * Set up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new EqualOperatorSniff();
    }
}
