<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_CLASS;
use const T_CONST;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_TRAIT;
use const T_VARIABLE;

/**
 * Class RequiredDocBlockSniffTest
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class RequiredDocBlockSniffTest extends SniffTestCase
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
            'CODE_MISSING_DOC_BLOCK_PREFIX' => ['CODE_MISSING_DOC_BLOCK_PREFIX', 'MissingDocBlock'],
            'CODE_NO_MULTI_LINE_DOC_BLOCK_PREFIX' => ['CODE_NO_MULTI_LINE_DOC_BLOCK_PREFIX', 'NoMultiLineDocBlock'],
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
            T_CLASS,
            T_CONST,
            T_INTERFACE,
            T_FUNCTION,
            T_TRAIT,
            T_VARIABLE
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

        $this->fixture = new RequiredDocBlockSniff();
    }
}
