<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\SniffTestCase;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff as BaseReturnTypeHintSpacingSniff;

/**
 * Tests ReturnTypeHintSpacingSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class ReturnTypeHintSpacingSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;

    /**
     * The tested class.
     *
     * @var ReturnTypeHintSpacingSniff
     */
    private ReturnTypeHintSpacingSniff $fixture;

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->fixture = new ReturnTypeHintSpacingSniff();
    }

    /**
     * Checks the inheritance of  the class.
     *
     * @return void
     */
    public function testInstance(): void
    {
        static::assertInstanceOf(BaseReturnTypeHintSpacingSniff::class, $this->fixture);
    }
}
