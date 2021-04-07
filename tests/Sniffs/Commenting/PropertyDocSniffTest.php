<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use const T_VARIABLE;

/**
 * Integration test for PropertySummarySniffTest.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class PropertyDocSniffTest extends AbstractDocSniffTest
{
    /**
     * Returns the tokens which should be checked.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_VARIABLE,
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

        $this->testedObject = new PropertyDocSniff();
    }
}
