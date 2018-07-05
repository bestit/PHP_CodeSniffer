<?php

declare(strict_types=1);

namespace Tests\BestIt\Sniffs\Formatting;

use BestIt\Sniffs\Formatting\SingleObjectOperatorSniff;
use PHP_CodeSniffer\Files\File;
use Tests\BestIt\SniffTestCase;

/**
 * Tests the single object operator
 *
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 * @package Tests\BestIt\Sniffs\Formatting
 */
class SingleObjectOperatorSniffTest extends SniffTestCase
{
    /**
     * Test all valid operator positions.
     *
     * @return void
     */
    public function testValidOperators()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkSniffFile($this->getFixtureFilePath('Correct.php'))
        );
    }

    /**
     * Test all invalid operator positions.
     *
     * @return void
     */
    public function testInvalidOperator()
    {
        $report = $this->checkSniffFile(
            $this->getFixtureFilePath('NoSingleObjectOperator.php')
        );

        foreach ([9, 11, 13, 15, 17, 20] as $line) {
            $this->assertSniffError(
                $report,
                $line,
                SingleObjectOperatorSniff::CODE_NOT_SINGLE_OBJECT_OPERATOR_STATEMENT
            );
        }

        $this->assertAllFixedInFile($report);
    }

    /**
     * Checks the given file with defined error codes.
     *
     * @param string $file Filename of the fixture
     * @param array $sniffProperties Array of sniff properties
     *
     * @return File The php cs file
     */
    protected function checkSniffFile(string $file, array $sniffProperties = []): File
    {
        return $this->checkFile(
            $file,
            $sniffProperties,
            [
                SingleObjectOperatorSniff::CODE_NOT_SINGLE_OBJECT_OPERATOR_STATEMENT
            ]
        );
    }
}
