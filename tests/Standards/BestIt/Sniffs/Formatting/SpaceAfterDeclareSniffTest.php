<?php

declare(strict_types=1);

namespace Tests\BestIt\Sniffs\Formatting;

use BestIt\Sniffs\Formatting\SpaceAfterDeclareSniff;
use PHP_CodeSniffer_File;
use Tests\BestIt\Sniffs\TestCase;

/**
 * Class SpaceAfterDeclareSniffTest
 *
 * @package Tests\BestIt\Sniffs\Formatting
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class SpaceAfterDeclareSniffTest extends TestCase
{
    /**
     * Test space after declare with no errors.
     *
     * @return void
     */
    public function testSpaceAfterDeclareCorrect()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkSpaceAfterDeclareFile(__DIR__ . '/Fixtures/SpaceAfterDeclare.Correct.php')
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
            $this->checkSpaceAfterDeclareFile(__DIR__ . '/Fixtures/SpaceAfterDeclare.NoFollowingStatement.php')
        );
    }

    /**
     * Test no whitespace found error and fix.
     *
     * @return void
     */
    public function testNoWhitespaceFound()
    {
        $report = $this->checkSpaceAfterDeclareFile(__DIR__ . '/Fixtures/SpaceAfterDeclare.NoWhitespaceFound.php');

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
        $report = $this->checkSpaceAfterDeclareFile(__DIR__ . '/Fixtures/SpaceAfterDeclare.MuchWhitespaceFound.php');

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
        $report = $this->checkSpaceAfterDeclareFile(
            __DIR__ . '/Fixtures/SpaceAfterDeclare.MultipleDeclareStatements.php'
        );

        $this->assertSniffError(
            $report,
            3,
            SpaceAfterDeclareSniff::CODE_NO_WHITESPACE_FOUND
        );

        $this->assertSniffError(
            $report,
            4,
            SpaceAfterDeclareSniff::CODE_NO_WHITESPACE_FOUND
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
    private function checkSpaceAfterDeclareFile($file)
    {
        return $this->checkFile(
            $file,
            [],
            [
                SpaceAfterDeclareSniff::CODE_NO_WHITESPACE_FOUND,
                SpaceAfterDeclareSniff::CODE_MUCH_WHITESPACE_FOUND,
            ]
        );
    }
}
