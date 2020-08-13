<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\SniffTestCase;
use PHP_CodeSniffer\Files\File;

/**
 * Class OpenTagSniffTest
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class OpenTagSniffTest extends SniffTestCase
{
    /**
     * Test empty line after open tag with no errors.
     *
     * @return void
     */
    public function testCorrectSpaceAfterOpenTag(): void
    {
        $this->assertNoSniffErrorInFile(
            $this->checkFile($this->getFixtureFilePath('Correct.php')),
        );
    }

    /**
     * Test empty line after open tag line not empty error and fix.
     *
     * @return void
     */
    public function testLineNotEmpty(): void
    {
        $report = $this->checkFile(
            $this->getFixtureFilePath('LineNotEmpty.php'),
        );

        $this->assertSniffError(
            $report,
            2,
            OpenTagSniff::CODE_LINE_NOT_EMPTY,
        );

        $this->assertAllFixedInFile($report);
    }

    /**
     * Test empty line after open tag no space after open tag error and fix.
     *
     * @return void
     */
    public function testNoSpaceAfterOpenTag(): void
    {
        $report = $this->checkFile(
            $this->getFixtureFilePath('NoSpaceAfterOpenTag.php'),
        );

        $this->assertSniffError(
            $report,
            2,
            OpenTagSniff::CODE_NO_SPACE_AFTER_OPEN_TAG,
        );

        $this->assertAllFixedInFile($report);
    }

    /**
     * Test empty line after open tag not first statement error and fix.
     *
     * @return void
     */
    public function testNotFirstStatement(): void
    {
        $report = $this->checkFile(
            $this->getFixtureFilePath('NotFirstStatement.php'),
        );

        $this->assertSniffError(
            $report,
            1,
            OpenTagSniff::CODE_NOT_FIRST_STATEMENT,
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
                OpenTagSniff::CODE_LINE_NOT_EMPTY,
                OpenTagSniff::CODE_NO_SPACE_AFTER_OPEN_TAG,
                OpenTagSniff::CODE_NOT_FIRST_STATEMENT,
            ],
        );
    }
}
