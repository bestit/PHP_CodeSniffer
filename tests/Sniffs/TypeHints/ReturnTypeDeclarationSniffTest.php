<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints;

use BestIt\SniffTestCase;
use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\TestRequiredConstantsTrait;
use const T_FUNCTION;

/**
 * Test for ReturnTypeDeclarationSniff
 *
 * @author Stephan Weber <stephan.weber@bestit-online.de>
 * @package BestIt\Sniffs\TypeHints
 */
class ReturnTypeDeclarationSniffTest extends SniffTestCase
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
        return [T_FUNCTION];
    }

    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_MISSING_RETURN_TYPE_HINT' => ['CODE_MISSING_RETURN_TYPE_HINT', 'MissingReturnTypeHint']
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

        $this->fixture = new ReturnTypeDeclarationSniff();
    }
}
