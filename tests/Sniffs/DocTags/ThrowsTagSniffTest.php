<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

/**
 * Checks the sniff for the throws tags.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class ThrowsTagSniffTest extends AuthorTagSniffTest
{
    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return parent::getRequiredConstantAsserts() + [
            'CODE_TAG_MISSING_DESC_DESC' => ['CODE_TAG_MISSING_DESC_DESC', 'MissingThrowDescription']
        ];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fixture = new ThrowsTagSniff();
    }
}
