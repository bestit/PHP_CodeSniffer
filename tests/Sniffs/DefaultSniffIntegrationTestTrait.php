<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use PHP_CodeSniffer\Files\File;
use function array_merge;
use function array_reverse;
use function explode;
use function preg_match;
use function range;
use function sprintf;
use function str_replace;
use function strpos;

/**
 * The basic calls for checking sniffs against files.
 *
 * @author Bjoern Lange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait DefaultSniffIntegrationTestTrait
{
    /**
     * Returns a list of files which start with correct*
     *
     * @return array With the path to a file as the first parameter.
     */
    public function getCorrectFileListAsDataProvider(): array
    {
        $providerFiles = [];

        foreach (glob($this->getFixturePath() . '/correct/*.php') as $file) {
            $providerFiles[basename($file)] = [$file];
        }

        return $providerFiles;
    }

    /**
     * Returns the metadata from the given file name if there is one.
     *
     * @param string $file
     * @param array $errorData This method changes a marker for other files, if there is a file with a fixed marker.
     *
     * @return array<string, string, ...int>
     */
    protected function getMetadataFromFilenameAsAssertArray(string $file, array &$errorData): array
    {
        $fileMetaData = [];
        $fileName = basename($file);
        $matches = [];
        $pattern = '/(?P<code>\w+)(\(\w*\))?\.(?P<errorLines>[\d\-\,]*)(?P<fixedSuffix>\.fixed)?\.php/';

        if (preg_match($pattern, $fileName, $matches)) {
            if (@$matches['fixedSuffix']) {
                @$errorData[str_replace('.fixed', '', $fileName)][] = true;
            } else {
                $errorLines = explode(',', $matches['errorLines']);

                // Check if there is a range.
                foreach ($errorLines as $index => $errorLine) {
                    if (strpos($errorLine, '-') !== false) {
                        unset($errorLines[$index]);

                        $errorLines = array_merge($errorLines, range(...explode('-', $errorLine)));
                    }
                }

                $fileMetaData = [
                    $file,
                    $matches['code'],
                    array_map('intval', $errorLines),
                ];
            }
        }

        return $fileMetaData;
    }

    /**
     * Test that the given files contain no errors.
     *
     * @dataProvider getCorrectFileListAsDataProvider
     * @param string $file Provided file to test
     *
     * @return void
     */
    public function testCorrect(string $file): void
    {
        $this->assertFileCorrect($file);
    }

    /**
     * Tests files which have to be without errors.
     *
     * @param string $file File to test
     *
     * @return void
     */
    abstract protected function assertFileCorrect(string $file): void;

    /**
     * Tests errors.
     *
     * @dataProvider getErrorAsserts
     *
     * @param string $file Fixture file
     * @param string $error Error code
     * @param int[] $lines Lines where the error code occurs
     * @param bool $withFixable Should we checked the fixed version?
     *
     * @return void
     */
    public function testErrors(string $file, string $error, array $lines, bool $withFixable = false): void
    {
        $report = $this->assertErrorsInFile($file, $error, $lines);

        if ($withFixable) {
            $this->assertAllFixedInFile($report);
        }
    }

    /**
     * Asserts all errors in a given file.
     *
     * @param string $file Filename of the fixture
     * @param string $error Error code
     * @param int[] $lines Array of lines where the error code occurs
     * @param array $sniffProperties Array of sniff properties
     *
     * @return File The php cs file
     */
    abstract protected function assertErrorsInFile(
        string $file,
        string $error,
        array $lines,
        array $sniffProperties = []
    ): File;

    /**
     * Tests warnings.
     *
     * @dataProvider getWarningAsserts
     *
     * @param string $file Fixture file
     * @param string $warning Code of the warning.
     * @param int[] $lines Lines where the error code occurs
     * @param bool $withFixable Should we test a fixable?
     *
     * @return void
     */
    public function testWarnings(string $file, string $warning, array $lines, bool $withFixable = false): void
    {
        $report = $this->assertWarningsInFile($file, $warning, $lines);

        if ($withFixable) {
            $this->assertAllFixedInFile($report);
        }
    }

    /**
     * Returns data for errors.
     *
     * @return array List of error data (Filepath, error code, error lines, fixable)
     */
    public function getErrorAsserts(): array
    {
        return $this->loadAssertData();
    }

    /**
     * Loads the assertion data out of the file names.
     *
     * The file name gives information about which errors in which line should occur.
     * Example files would be ErrorCode.1.php, ErrorCode.1,2,3.php, ErrorCode.1,2,3.fixed.php. The error code must be
     * the original code value from your sniff, the numbers after the first dot are the erroneous lines.
     *
     * If you provide an additional file which is suffixed with "fixed" then this is the correct formatted file for its
     * erroneous sibling.
     *
     * @param bool $forErrors Load data for errors?
     *
     * @return array The assert data as data providers.
     */
    private function loadAssertData(bool $forErrors = true): array
    {
        $errorData = [];

        foreach ($this->getFixtureFiles($forErrors) as $file) {
            if ($fileMetaData = $this->getMetadataFromFilenameAsAssertArray($file, $errorData)) {
                $errorData[basename($file)] = $fileMetaData;
            }
        }

        return $errorData;
    }

    /***
     * Returns the test files for errors or warnings.
     *
     * @param bool $forErrors Load data for errors?
     *
     * @return array The testable files.
     */
    private function getFixtureFiles(bool $forErrors = true): array
    {
        return array_reverse(glob(sprintf(
            $this->getFixturePath() . '/with_%s/*.php',
            $forErrors ? 'errors' : 'warnings',
        ))) ?: [];
    }

    /**
     * Returns data for warnings.
     *
     * @return array List of warnig data (Filepath, error code, error lines, fixable)
     */
    public function getWarningAsserts(): array
    {
        return $this->loadAssertData(false);
    }

    /**
     * Asserts all warnings in a given file.
     *
     * @throws Exception
     *
     * @param string $file Filename of the fixture
     * @param string $error Error code
     * @param int[] $lines Array of lines where the error code occurs
     * @param array $sniffProperties Array of sniff properties
     *
     * @return File The php cs file
     */
    abstract protected function assertWarningsInFile(
        string $file,
        string $error,
        array $lines,
        array $sniffProperties = []
    ): File;
}
