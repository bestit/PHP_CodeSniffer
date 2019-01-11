<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\SniffTestCase;
use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\TestRequiredConstantsTrait;
use const T_DOC_COMMENT_STAR;

/**
 * Class RedundantWhitespaceSniffTest
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class RedundantWhitespaceSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestTokenRegistrationTrait;
    use TestRequiredConstantsTrait;

    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_ERROR_REDUNDANT_WHITESPACE' => ['CODE_ERROR_REDUNDANT_WHITESPACE', 'RedundantWhitespace']
        ];
    }

    /**
     * Returns the tokens which should be checked.
     *
     * @return array Returns the expected token ids.
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_DOC_COMMENT_STAR
        ];
    }

    /**
     * Sets upo the test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fixture = new RedundantWhitespaceSniff();
    }
}
