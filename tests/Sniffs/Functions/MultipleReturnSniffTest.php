<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_FUNCTION;

/**
 * Class MultipleReturnSniffTest.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class MultipleReturnSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Returns the expected tokens for registration.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [T_FUNCTION];
    }

    /**
     * Returns the required constants.
     *
     * @return array
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_MULTIPLE_RETURNS_FOUND' => ['CODE_MULTIPLE_RETURNS_FOUND', 'MultipleReturnsFound'],
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

        $this->testedObject = new MultipleReturnSniff();
    }
}
