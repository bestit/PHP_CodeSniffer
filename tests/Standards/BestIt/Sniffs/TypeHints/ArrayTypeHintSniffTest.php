<?php

declare(strict_types = 1);

namespace Tests\BestIt\Sniffs\TypeHints;

use BestIt\Sniffs\TypeHints\ArrayTypeHintSniff;
use PHP_CodeSniffer\Files\File;
use Tests\BestIt\SniffTestCase;

/**
 * Tests the array type hint sniff
 *
 * @package Tests\BestIt\Sniffs\TypeHints
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 */
class ArrayTypeHintSniffTest extends SniffTestCase
{
    /**
     * Test type hints with no warnings.
     *
     * @return void
     */
    public function testCorrectTypeHints()
    {
        $this->assertNoSniffWarningInFile(
            $this->checkSniffFile($this->getFixtureFilePath('Correct.php'))
        );
    }

    /**
     * Test type hints with warnings.
     *
     * @return void
     */
    public function testWrongTypeHints()
    {
        $this->assertEquals(
            2,
            $this->checkSniffFile($this->getFixtureFilePath('GenericArray.php'))->getWarningCount()
        );
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
                ArrayTypeHintSniff::CODE_GENERIC_ARRAY
            ]
        );
    }

    /**
     * Check if sniff has no warnings
     *
     * @param File $file
     *
     * @return void
     */
    private function assertNoSniffWarningInFile(File $file)
    {
        $warnings = $file->getWarnings();
        $this->assertEmpty($warnings, sprintf('No warnings expected, but %d warnings found.', count($warnings)));
    }
}
