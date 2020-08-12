<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_VAR;

/**
 * Checks NoSimplePropertyMethodSniff.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class NoSimplePropertyMethodSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * The tested class.
     *
     * We use this var to reduce the hard dependencies on internals from a specific slevomat version.
     *
     * @var NoSimplePropertyMethodSniff|void
     */
    protected $fixture;

    /**
     * We should check only functions.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_VAR,
            T_PUBLIC,
            T_PROTECTED,
            T_PRIVATE,
        ];
    }

    /**
     * Checks if the error codes are there.
     *
     * @return array
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_SHOULD_USE_PROPERTY' => [
                'CODE_SHOULD_USE_PROPERTY',
                'ShouldUseTypedPropertyDirectly',
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

        $this->fixture = new NoSimplePropertyMethodSniff();
    }
}
