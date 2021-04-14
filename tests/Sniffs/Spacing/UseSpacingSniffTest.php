<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_OPEN_TAG;

/**
 * Test UseSpacingSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Spacing
 */
class UseSpacingSniffTest extends SniffTestCase
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
            T_OPEN_TAG,
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
            'CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE' => [
                'CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_USE',
                'IncorrectLinesCountBeforeFirstUse',
            ],
            'CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE' => [
                'CODE_INCORRECT_LINES_COUNT_BETWEEN_SAME_TYPES_OF_USE',
                'IncorrectLinesCountBetweenSameTypeOfUse',
            ],
            'CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE' => [
                'CODE_INCORRECT_LINES_COUNT_BETWEEN_DIFFERENT_TYPES_OF_USE',
                'IncorrectLinesCountBetweenDifferentTypeOfUse',
            ],
            'CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE' => [
                'CODE_INCORRECT_LINES_COUNT_AFTER_LAST_USE',
                'IncorrectLinesCountAfterLastUse',
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

        $this->fixture = new UseSpacingSniff();
    }
}
