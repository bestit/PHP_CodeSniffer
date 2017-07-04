<?php

declare(strict_types=1);

namespace Tests\BestIt\Sniffs\Commenting;

use BestIt\Sniffs\Commenting\MethodDocSniff;
use PHP_CodeSniffer_File;
use Tests\BestIt\Sniffs\TestCase;

/**
 * Class MethodDocSniffTest
 *
 * @package Tests\BestIt\Sniffs\Functions
 *
 * @author Nils Hardeweg <nils.hardeweg@bestit-online.de>
 */
class MethodDocSniffTest extends TestCase
{
    /**
     * Test fluent setter with no errors.
     *
     * @return void
     */
    public function testCorrectShortSummary()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkMethodDocSniff(__DIR__ . '/Fixtures/MethodDocSniff.Correct.php')
        );
    }

    /**
     * Test fluent setter with no errors.
     *
     * @return void
     */
    public function testInheritDoc()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkMethodDocSniff(__DIR__ . '/Fixtures/MethodDocSniff.Inheritdoc.php')
        );
    }

    /**
     * Test fluent setter with no errors.
     *
     * @return void
     */
    public function testInlineDoc()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkMethodDocSniff(__DIR__ . '/Fixtures/MethodDocSniff.InlineDoc.php')
        );
    }

    /**
     * Test empty method doc block
     *
     * @return void
     */
    public function testEmpty()
    {
        $report = $this->checkMethodDocSniff(__DIR__ . '/Fixtures/MethodDocSniff.Empty.php');

        $this->assertSniffError(
            $report,
            5,
            MethodDocSniff::CODE_EMPTY
        );
    }

    /**
     * Test missing empty line after summary
     *
     * @return void
     */
    public function testMissingEmptyLineAfterSummary()
    {
        $report = $this->checkMethodDocSniff(__DIR__ . '/Fixtures/MethodDocSniff.MissingEmptyLineAfterSummary.php');

        $this->assertSniffError(
            $report,
            7,
            MethodDocSniff::CODE_SPACING_BEFORE_TAGS
        );

        $this->assertSniffError(
            $report,
            15,
            MethodDocSniff::CODE_SPACING_BEFORE_TAGS
        );

        $this->assertSniffError(
            $report,
            27,
            MethodDocSniff::CODE_SPACING_BETWEEN
        );
    }

    /**
     * Test if a doc block is missing a summary
     *
     * @return void
     */
    public function testMissingSummary()
    {
        $report = $this->checkMethodDocSniff(__DIR__ . '/Fixtures/MethodDocSniff.MissingSummary.php');

        $this->assertSniffError(
            $report,
            5,
            MethodDocSniff::CODE_MISSING_SHORT
        );
    }

    /**
     * Test if a doc block is missing a summary
     *
     * @return void
     */
    public function testEmtpyLineBeforeSummary()
    {
        $report = $this->checkMethodDocSniff(__DIR__ . '/Fixtures/MethodDocSniff.NoLineBeforeSummary.php');

        $this->assertSniffError(
            $report,
            7,
            MethodDocSniff::CODE_SPACING_BEFORE_SHORT
        );
    }

    /**
     * Tests if the doc block short description (summary) and long description is starting with a capital letter
     *
     * @return void
     */
    public function testNonCapitalLetterStart()
    {
        $report = $this->checkMethodDocSniff(__DIR__ . '/Fixtures/MethodDocSniff.NonCapitalLetterStart.php');

        $this->assertSniffError(
            $report,
            6,
            MethodDocSniff::CODE_SHORT_NOT_CAPITAL
        );

        $this->assertSniffError(
            $report,
            15,
            MethodDocSniff::CODE_SHORT_NOT_CAPITAL
        );

        $this->assertSniffError(
            $report,
            17,
            MethodDocSniff::CODE_LONG_NOT_CAPITAL
        );
    }


    /**
     * Return a PHP_CodeSniffer_File with only needed sniff codes.
     *
     * @param string $file
     *
     * @return PHP_CodeSniffer_File
     */
    private function checkMethodDocSniff($file)
    {
        return $this->checkFile(
            $file,
            [],
            [
                MethodDocSniff::CODE_EMPTY,
                MethodDocSniff::CODE_SPACING_BEFORE_TAGS,
                MethodDocSniff::CODE_SPACING_BETWEEN,
                MethodDocSniff::CODE_MISSING_SHORT,
                MethodDocSniff::CODE_SHORT_NOT_CAPITAL,
                MethodDocSniff::CODE_LONG_NOT_CAPITAL,
                MethodDocSniff::CODE_SPACING_BEFORE_SHORT
            ]
        );
    }
}
