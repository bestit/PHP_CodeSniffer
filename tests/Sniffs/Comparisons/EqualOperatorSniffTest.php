<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons;

use BestIt\SniffTestCase;
use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\TestRequiredConstantsTrait;

/**
 * Class EqualOperatorSniffTest.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\Comparisons
 */
class EqualOperatorSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Get the expected tokens.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [T_IS_EQUAL];
    }

    /**
     * Get required constants.
     *
     * @return array
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_EQUAL_OPERATOR_FOUND' => ['CODE_EQUAL_OPERATOR_FOUND', 'EqualOperatorFound'],
        ];
    }

    /**
     * Set up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new EqualOperatorSniff();
    }

    /**
     * Tests errors.
     *
     * @dataProvider getErrorAsserts
     *
     * @param string $file Fixture file
     * @param string $warning Error code
     * @param int[] $lines Lines where the error code occurs
     * @param bool $withFixable Should we checked the fixed version?
     *
     * @return void
     */
    public function testWarnings(string $file, string $warning, array $lines, bool $withFixable = false): void
    {
        $report = $this->assertWarningsInFile($file, $warning, $lines, ['isFixable' => true]);

        if ($withFixable) {
            $this->assertAllFixedInFile($report);
        }
    }

    /**
     * Tests errors.
     *
     * @dataProvider getErrorAsserts
     *
     * @param string $file Fixture file
     * @param string $error Error code
     * @param int[] $lines Lines where the error code occurs
     *
     * @return void
     */
    public function testWarningsWithoutFix(string $file, string $error, array $lines): void
    {
        $this->assertWarningsInFile($file, $error, $lines);
    }
}
