<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_VAR;

/**
 * Test PropertySpacingSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Spacing
 */
class PropertySpacingSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestTokenRegistrationTrait;
    use TestRequiredConstantsTrait;

    /**
     * Which tokens are expected?
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_VAR,
            T_PUBLIC,
            T_PROTECTED,
            T_PRIVATE,
        ];
    }

    /**
     * Checks which constants are required.
     *
     * @return iterable
     */
    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY' => [
                'CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY',
                'IncorrectCountOfBlankLinesAfterProperty',
            ],
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

        $this->testedObject = new PropertySpacingSniff();
    }
}
