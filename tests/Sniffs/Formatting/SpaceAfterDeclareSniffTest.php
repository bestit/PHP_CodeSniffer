<?php

declare(strict_types = 1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\Formatting\SpaceAfterDeclareSniff;
use PHP_CodeSniffer\Files\File;
use BestIt\SniffTestCase;

/**
 * Class SpaceAfterDeclareSniffTest
 *
 * @package BestIt\Sniffs\Formatting
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class SpaceAfterDeclareSniffTest extends SniffTestCase
{
    /**
     * Test space after declare with no errors.
     *
     * @return void
     */
    public function testSpaceAfterDeclareCorrect()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkSniffFile($this->getFixtureFilePath('Correct.php'))
        );
    }

    /**
     * Test early return when no following statement is found.
     *
     * @return void
     */
    public function testEarlyReturn()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkSniffFile($this->getFixtureFilePath('NoFollowingStatement.php'))
        );
    }

    /**
     * Test no whitespace found error and fix.
     *
     * @return void
     */
    public function testNoWhitespaceFound()
    {
        $report = $this->checkSniffFile($this->getFixtureFilePath('NoWhitespaceFound.php'));

        $this->assertSniffError(
            $report,
            3,
            SpaceAfterDeclareSniff::CODE_NO_WHITESPACE_FOUND
        );

        $this->assertAllFixedInFile($report);
    }

    /**
     * Test much whitespaces found error and fix.
     *
     * @return void
     */
    public function testMuchWhitespaceFound()
    {
        $report = $this->checkSniffFile($this->getFixtureFilePath('MuchWhitespaceFound.php'));

        $this->assertSniffError(
            $report,
            3,
            SpaceAfterDeclareSniff::CODE_MUCH_WHITESPACE_FOUND
        );

        $this->assertAllFixedInFile($report);
    }

    /**
     * Test multiple declare statements.
     *
     * @return void
     */
    public function testMultipleDeclareStatements()
    {
        $report = $this->checkSniffFile(
            $this->getFixtureFilePath('MultipleDeclareStatements.php')
        );

        $this->assertSniffError(
            $report,
            3,
            SpaceAfterDeclareSniff::CODE_GROUP_BLANK_LINE_FOUND
        );

        $this->assertSniffError(
            $report,
            5,
            SpaceAfterDeclareSniff::CODE_NO_WHITESPACE_FOUND
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
    protected function checkSniffFile(string $file, array $sniffProperties = []): File
    {
        return $this->checkFile(
            $file,
            $sniffProperties,
            [
                SpaceAfterDeclareSniff::CODE_NO_WHITESPACE_FOUND,
                SpaceAfterDeclareSniff::CODE_MUCH_WHITESPACE_FOUND,
                SpaceAfterDeclareSniff::CODE_GROUP_BLANK_LINE_FOUND,
            ]
        );
    }
}
