<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

/**
 * Checks the sniff for the version tags.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class VersionTagSniffTest extends AuthorTagSniffTest
{
    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fixture = new VersionTagSniff();
    }
}
