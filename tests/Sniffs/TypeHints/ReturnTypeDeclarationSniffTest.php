<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints;

use BestIt\SniffTestCase;
use PHP_CodeSniffer\Files\File;

/**
 * Test for ReturnTypeDeclarationSniff
 *
 * @author Stephan Weber <stephan.weber@bestit-online.de>
 * @package BestIt\Sniffs\TypeHints
 */
class ReturnTypeDeclarationSniffTest extends SniffTestCase
{
    /**
     * Test type hints with no errors.
     *
     * @return void
     */
    public function testCorrectTypeHints()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkFile($this->getFixtureFilePath('Correct.php'))
        );
    }

    /**
     * Test missing type hints and fix.
     *
     * @return void
     */
    public function testNoTypeHints()
    {
        $report = $this->checkFile($this->getFixtureFilePath('NoTypeHints.php'));

        $this->assertSniffError(
            $report,
            8,
            ReturnTypeDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT
        );

        $this->assertSniffError(
            $report,
            16,
            ReturnTypeDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT
        );

        $this->assertSniffError(
            $report,
            24,
            ReturnTypeDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT
        );

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
    protected function checkFileAgainstSniff(string $file, array $sniffProperties = []): File
    {
        return $this->checkFile(
            $file,
            $sniffProperties,
            [
                ReturnTypeDeclarationSniff::CODE_MISSING_RETURN_TYPE_HINT
            ]
        );
    }
}
