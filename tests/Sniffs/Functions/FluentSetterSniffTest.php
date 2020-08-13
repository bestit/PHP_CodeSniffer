<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_FUNCTION;

/**
 * Class FluentSetterSniffTest
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class FluentSetterSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * The tested class.
     *
     * We use this var to reduce the hard dependencies on internals from a specific slevomat version.
     *
     * @var FluentSetterSniff|void
     */
    protected $fixture;

    /**
     * Returns the tokens which should be checked.
     *
     * @return array The expected token ids.
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_FUNCTION,
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

        $this->fixture = new FluentSetterSniff();
    }

    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_MUST_RETURN_THIS' => ['CODE_MUST_RETURN_THIS', 'MustReturnThis'],
            'CODE_NO_RETURN_FOUND' => ['CODE_NO_RETURN_FOUND', 'NoReturnFound'],
        ];
    }
}
