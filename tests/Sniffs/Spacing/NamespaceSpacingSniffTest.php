<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_NAMESPACE;

/**
 * Test NamespaceSpacingSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Spacing
 */
class NamespaceSpacingSniffTest extends SniffTestCase
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
            T_NAMESPACE,
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
            'CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE' => [
                'CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE',
                'IncorrectLinesCountBeforeNamespace',
            ],
            'CODE_INCORRECT_LINES_COUNT_AFTER_NAMESPACE' => [
                'CODE_INCORRECT_LINES_COUNT_AFTER_NAMESPACE',
                'IncorrectLinesCountAfterNamespace',
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

        $this->testedObject = new NamespaceSpacingSniff();
    }
}
