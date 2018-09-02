<?php

declare(strict_types = 1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\Functions\FluentSetterSniff;
use PHP_CodeSniffer\Files\File;
use BestIt\SniffTestCase;

/**
 * Class FluentSetterSniffTest
 *
 * @package BestIt\Sniffs\Functions
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class FluentSetterSniffTest extends SniffTestCase
{
    /**
     * Test fluent setter with no errors.
     *
     * @return void
     */
    public function testCorrectFluentSetter()
    {
        $this->assertNoSniffErrorInFile(
            $this->checkSniffFile($this->getFixtureFilePath('Correct.php'))
        );
    }

    /**
     * Test fluent setter no return error and fix.
     *
     * @return void
     */
    public function testFluentSetterNoReturn()
    {
        $report = $this->checkSniffFile($this->getFixtureFilePath('NoReturn.php'));

        $this->assertSniffError(
            $report,
            7,
            FluentSetterSniff::CODE_NO_RETURN_FOUND
        );

        $this->assertAllFixedInFile($report);
    }

    /**
     * Test fluent setter multiple return error.
     *
     * @return void
     */
    public function testFluentSetterMultipleReturn()
    {
        $this->assertSniffError(
            $this->checkSniffFile($this->getFixtureFilePath('MultipleReturn.php')),
            7,
            FluentSetterSniff::CODE_MULTIPLE_RETURN_FOUND
        );
    }

    /**
     * Test fluent setter must return this error and fix.
     *
     * @return void
     */
    public function testFluentSetterMustReturnThis()
    {
        $report = $this->checkSniffFile($this->getFixtureFilePath('MustReturnThis.php'));

        $this->assertSniffError(
            $report,
            7,
            FluentSetterSniff::CODE_MUST_RETURN_THIS
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
                FluentSetterSniff::CODE_MULTIPLE_RETURN_FOUND,
                FluentSetterSniff::CODE_MUST_RETURN_THIS,
                FluentSetterSniff::CODE_NO_RETURN_FOUND,
            ]
        );
    }
}
