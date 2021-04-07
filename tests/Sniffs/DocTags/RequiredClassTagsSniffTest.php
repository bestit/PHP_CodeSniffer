<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Checks the sniff for the class tags.
 *
 * @author blange <bjoern.lange@besti-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class RequiredClassTagsSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Returns the tokens which should be checked.
     *
     * @return array Returns the expected token ids.
     */
    protected function getExpectedTokens(): array
    {
        return Tokens::$ooScopeTokens;
    }

    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_TAG_OCCURRENCE_MIN_PREFIX' => ['CODE_TAG_OCCURRENCE_MIN_PREFIX', 'TagOccurrenceMin'],
            'CODE_TAG_OCCURRENCE_MAX_PREFIX' => ['CODE_TAG_OCCURRENCE_MAX_PREFIX', 'TagOccurrenceMax'],
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

        $this->testedObject = new RequiredClassTagsSniff();
    }
}
