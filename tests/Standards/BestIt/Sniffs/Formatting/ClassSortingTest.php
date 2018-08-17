<?php

namespace Tests\BestIt\Sniffs\Formatting;

use BestIt\Sniffs\Formatting\ClassSortingSniff;
use Tests\BestIt\SniffTestCase;
use PHP_CodeSniffer\Files\File;

/**
 * Class ClassSortingTest.
 * @package Tests\BestIt\Sniffs\Formatting
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
class ClassSortingSniffTest extends SniffTestCase
{
    /**
     * Test ClassSortingSniff without errors.
     */
    public function testCorrectClassSorting()
    {
        $result = $this->checkSniffFile($this->getFixtureFilePath('CorrectSorting.php'));
        static::assertCount(0, $result->getErrors());
    }

    /**
     * Test ClassSortingSniff without errors.
     */
    public function testWrongClassSorting()
    {
        $result = $this->checkSniffFile($this->getFixtureFilePath('WrongSorting.php'));
        static::assertCount(1, $result->getErrors());
    }

    /**
     * Check the sniff file.
     *
     * @param string $file
     * @param array $sniffProperties
     *
     * @return File
     */
    protected function checkSniffFile(string $file, array $sniffProperties = []): File
    {
        return $this->checkFile(
            $file,
            $sniffProperties,
            [
                ClassSortingSniff::CODE_WRONG_SORTING_FOUND
            ]
        );
    }
}
