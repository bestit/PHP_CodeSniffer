<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Class DisallowedClassTagsSniffTest.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class DisallowedClassTagsSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_TAG_NOT_ALLOWED' => ['CODE_TAG_NOT_ALLOWED', 'TagNotAllowed'],
        ];
    }

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
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new DisallowedClassTagsSniff();
    }

    /**
     * Checks if the api is fulfilled.
     *
     * @return void
     */
    public function testType(): void
    {
        static::assertInstanceOf(AbstractDisallowedTagsSniff::class, $this->fixture);
    }
}
