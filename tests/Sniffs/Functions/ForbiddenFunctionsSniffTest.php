<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\SniffTestCase;
use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;

/**
 * Class ForbiddenFunctionsSniffTest.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class ForbiddenFunctionsSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;

    /**
     * The tested class.
     *
     * We use this var to reduce the hard dependencies on internals from a specific slevomat version.
     *
     * @var ForbiddenFunctionsSniff|void
     */
    protected $fixture;

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fixture = new ForbiddenFunctionsSniff();
    }
}
