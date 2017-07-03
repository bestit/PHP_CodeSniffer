<?php

declare(strict_types=1);

namespace Tests\BestIt\Sniffs\Formatting;

use BestIt\Sniffs\Formatting\OpenTagSniff;
use PHP_CodeSniffer_File;
use Tests\BestIt\Sniffs\TestCase;

/**
 * Class OpenTagSniffTest
 *
 * @package Tests\BestIt\Sniffs\Formatting
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class OpenTagSniffTest extends TestCase
{
    /**
     * Test empty line after open tag with no errors.
     */
    public function testCorrectSpaceAfterOpenTag()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkOpenTagFile(__DIR__ . '/Fixtures/OpenTagSniff.Correct.php')
        );
    }

    /**
     * Test empty line after open tag line not empty error and fix.
     */
    public function testLineNotEmpty()
    {
        $report = $this->checkOpenTagFile(
            __DIR__ . '/Fixtures/OpenTagSniff.LineNotEmpty.php'
        );

        $this->assertSniffError(
            $report,
            2,
            OpenTagSniff::CODE_LINE_NOT_EMPTY
        );

        $this->assertAllFixedInFile($report);
    }

    /**
     * Test empty line after open tag no space after open tag error and fix.
     */
    public function testNoSpaceAfterOpenTag()
    {
        $report = $this->checkOpenTagFile(
            __DIR__ . '/Fixtures/OpenTagSniff.NoSpaceAfterOpenTag.php'
        );

        $this->assertSniffError(
            $report,
            2,
            OpenTagSniff::CODE_NO_SPACE_AFTER_OPEN_TAG
        );

        $this->assertAllFixedInFile($report);
    }

    /**
     * Test empty line after open tag not first statement error and fix.
     */
    public function testNotFirstStatement()
    {
        $report = $this->checkOpenTagFile(
            __DIR__ . '/Fixtures/OpenTagSniff.NotFirstStatement.php'
        );

        $this->assertSniffError(
            $report,
            1,
            OpenTagSniff::CODE_NOT_FIRST_STATEMENT
        );

        $this->assertAllFixedInFile($report);
    }

    /**
     * Return a PHP_CodeSniffer_File with only needed sniff codes.
     *
     * @param string $file
     *
     * @return PHP_CodeSniffer_File
     */
    private function checkOpenTagFile($file)
    {
        return $this->checkFile(
            $file,
            [],
            [
                OpenTagSniff::CODE_LINE_NOT_EMPTY,
                OpenTagSniff::CODE_NO_SPACE_AFTER_OPEN_TAG,
                OpenTagSniff::CODE_NOT_FIRST_STATEMENT,
            ]
        );
    }
}
