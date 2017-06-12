<?php

namespace Tests\BestIt\Sniffs\Functions;

use BestIt\Sniffs\Functions\FluentSetterSniff;
use PHP_CodeSniffer_File;
use Tests\BestIt\Sniffs\TestCase;

/**
 * Class FluentSetterSniffTest
 *
 * @package Tests\BestIt\Sniffs\Functions
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class FluentSetterSniffTest extends TestCase
{
    /**
     * Test fluent setter with no errors.
     */
    public function testCorrectFluentSetter()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkFluentSetterFile(__DIR__ . '/Fixtures/FluentSetterSniff.Correct.php')
        );
    }

    /**
     * Test fluent setter no return error and fix.
     */
    public function testFluentSetterNoReturn()
    {
        $report = $this->checkFluentSetterFile(__DIR__ . '/Fixtures/FluentSetterSniff.NoReturn.php');

        $this->assertSniffError(
            $report,
            7,
            FluentSetterSniff::CODE_NO_RETURN_FOUND
        );

        $this->assertAllFixedInFile($report);
    }

    /**
     * Test fluent setter multiple return error.
     */
    public function testFluentSetterMultipleReturn()
    {
        $this->assertSniffError(
            $this->checkFluentSetterFile(__DIR__ . '/Fixtures/FluentSetterSniff.MultipleReturn.php'),
            7,
            FluentSetterSniff::CODE_MULTIPLE_RETURN_FOUND
        );
    }

    /**
     * Test fluent setter must return this error and fix.
     */
    public function testFluentSetterMustReturnThis()
    {
        $report = $this->checkFluentSetterFile(__DIR__ . '/Fixtures/FluentSetterSniff.MustReturnThis.php');

        $this->assertSniffError(
            $report,
            7,
            FluentSetterSniff::CODE_MUST_RETURN_THIS
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
    private function checkFluentSetterFile($file)
    {
        return $this->checkFile(
            $file,
            [],
            [
                FluentSetterSniff::CODE_MULTIPLE_RETURN_FOUND,
                FluentSetterSniff::CODE_MUST_RETURN_THIS,
                FluentSetterSniff::CODE_NO_RETURN_FOUND,
            ]
        );
    }
}
