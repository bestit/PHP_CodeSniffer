<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\SniffTestCase;

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
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new ForbiddenFunctionsSniff();
    }
}
