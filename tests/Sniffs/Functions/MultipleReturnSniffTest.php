<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;

/**
 * Class MultipleReturnSniffTest.
 *
 * @package BestIt\Sniffs\Functions
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
class MultipleReturnSniffTest extends SniffTestCase
{
    use TestTokenRegistrationTrait;
    use DefaultSniffIntegrationTestTrait;

    protected $fixture;

    protected function getExpectedTokens(): array
    {
        return [
            T_RETURN
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new MultipleReturnSniff();
    }

    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_MULTIPLE_RETURNS_FOUND' => ['CODE_MULTIPLE_RETURNS_FOUND', 'MultipleReturnsFound'],
        ];
    }
}
