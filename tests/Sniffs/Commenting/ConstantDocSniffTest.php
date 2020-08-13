<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use const T_CONST;

/**
 * Integration test for ConstantSummarySniffTest.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package  BestIt\Sniffs\Commenting
 * @see ConstantDocSniff
 */
class ConstantDocSniffTest extends AbstractDocSniffTest
{
    /**
     * Returns the tokens which should be checked.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_CONST,
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

        $this->fixture = new ConstantDocSniff();
    }
}
