<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use PHP_CodeSniffer\Util\Tokens;

/**
 * Test for ClassSummarySniffTest.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 * @see ClassDocSniff
 */
class ClassDocSniffTest extends AbstractDocSniffTest
{
    /**
     * Returns the tokens which should be checked.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return Tokens::$ooScopeTokens;
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new ClassDocSniff();
    }
}
