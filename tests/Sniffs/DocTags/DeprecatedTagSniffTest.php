<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\SniffTestCase;
use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\TestRequiredConstantsTrait;
use PHP_CodeSniffer\Util\Tokens;
use const T_DOC_COMMENT_TAG;

/**
 * Class DeprecatedTagSniffTest.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class DeprecatedTagSniffTest extends SniffTestCase
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
            'CODE_TAG_CONTENT_FORMAT_INVALID' => ['CODE_TAG_CONTENT_FORMAT_INVALID', 'TagContentFormatInvalid'],
            'CODE_TAG_MISSING_DATES' => ['CODE_TAG_MISSING_DATES', 'MissingDates']
        ];
    }

    /**
     * Returns the tokens which should be checked.
     *
     * @return array Returns the expected token ids.
     */
    protected function getExpectedTokens(): array
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new DeprecatedTagSniff();
    }
}
