<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use const T_CONST;

/**
 * Class DisallowedConstantTagsSniffTest.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class DisallowedConstantTagsSniffTest extends DisallowedClassTagsSniffTest
{
    /**
     * Returns the tokens which should be checked.
     *
     * @return array Returns the expected token ids.
     */
    protected function getExpectedTokens(): array
    {
        return [T_CONST];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new DisallowedConstantTagsSniff();
    }
}
