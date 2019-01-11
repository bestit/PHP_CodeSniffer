<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use const T_FUNCTION;

/**
 * Class RequiredMethodTagsSniffTest.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class RequiredMethodTagsSniffTest extends RequiredClassTagsSniffTest
{
    /**
     * Returns the tokens which should be checked.
     *
     * @return array Returns the expected token ids.
     */
    protected function getExpectedTokens(): array
    {
        return [T_FUNCTION];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fixture = new RequiredMethodTagsSniff();
    }
}
