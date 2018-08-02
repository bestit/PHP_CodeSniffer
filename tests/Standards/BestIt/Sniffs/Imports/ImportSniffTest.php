<?php

declare(strict_types = 1);

namespace Tests\BestIt\Sniffs\Imports;

use BestIt\Sniffs\Functions\FluentSetterSniff;
use BestIt\Sniffs\Imports\ImportSniff;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Tests\Core\ErrorSuppressionTest;
use PHPUnit\Framework\Error\Error;
use Tests\BestIt\SniffTestCase;

/**
 * Class ImportSniffTest.
 * @package Tests\BestIt\Sniffs\Imports
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
class ImportSniffTest extends SniffTestCase
{
    /**
     * Test fluent setter with no errors.
     *
     * @return void
     */
    public function testCorrectFluentSetter()
    {
        $result = $this->checkSniffFile($this->getFixtureFilePath('Import.php'));
        static::assertCount(1, $result->getErrors());
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
                ImportSniff::CODE_FQN_FOUND
            ]
        );
    }
}
