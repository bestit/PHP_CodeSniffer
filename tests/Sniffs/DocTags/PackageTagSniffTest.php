<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

/**
 * Checks the sniff for the package tag.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class PackageTagSniffTest extends AuthorTagSniffTest
{
    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->testedObject = new PackageTagSniff();
    }
}
