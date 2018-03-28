<?php

declare(strict_types=1);

namespace Tests\BestIt;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Sniffs\TestCase as SlevomatTestCase;

/**
 * Class SniffTestCase
 *
 * @package Tests\BestIt\Sniffs
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
abstract class SniffTestCase extends SlevomatTestCase
{
    /**
     * Returns the sniff class name.
     *
     * @return string sniff class name
     */
    protected function getSniffClassName(): string
    {
        $className = get_class($this);

        $className = str_replace('Tests\\', '', $className);

        return substr($className, 0, -strlen('Test'));
    }

    /**
     * Asserts that all errors are fixed in the given file.
     *
     * @param File $codeSnifferFile The CodeSniffer file
     *
     * @return void
     */
    protected function assertAllFixedInFile(File $codeSnifferFile)
    {
        $codeSnifferFile->fixer->fixFile();

        $this->assertStringEqualsFile(
            preg_replace('~(\\.php)$~', '.Fixed\\1', $codeSnifferFile->getFilename()),
            $codeSnifferFile->fixer->getContents()
        );
    }

    /**
     * Tests files with given error list and fixes them.
     *
     * @param string $file File to test
     * @param string $error Error code
     * @param int[] $lines All lines where the error code occurs
     * @param array $sniffProperties Array of sniff properties
     *
     * @return void
     */
    protected function assertFixableErrorsInFile(
        string $file,
        string $error,
        array $lines,
        array $sniffProperties = []
    ) {
        $report = $this->assertErrorsInFile($file, $error, $lines, $sniffProperties);

        $this->assertAllFixedInFile($report);
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
    protected function assertErrorsInFile(
        string $file,
        string $error,
        array $lines,
        array $sniffProperties = []
    ): File {
        $report = $this->checkSniffFile(
            $this->getFixtureFilePath($file),
            $sniffProperties
        );

        foreach ($lines as $line) {
            $this->assertSniffError(
                $report,
                $line,
                $error
            );
        }

        return $report;
    }

    /**
     * Tests files which have to be without errors.
     *
     * @param string $file File to test
     *
     * @return void
     */
    protected function assertFileCorrect(string $file)
    {
        $report = $this->checkSniffFile(
            $this->getFixtureFilePath($file)
        );

        $this->assertNoSniffErrorInFile($report);
    }

    /**
     * Processes the fixture path.
     *
     * @return string Fixture path
     */
    public function getFixturePath(): string
    {
        $fqTestClassName = get_class($this);
        $fqTestClassNameParts  = explode('\\', $fqTestClassName);

        $sniffsIndex = array_search('BestIt', $fqTestClassNameParts, true);

        $additionalPathParts = array_slice($fqTestClassNameParts, $sniffsIndex + 1);

        $testClassName = array_pop($additionalPathParts);
        $className = substr($testClassName, 0, -4);

        $additionalPath = implode(DIRECTORY_SEPARATOR, $additionalPathParts);

        $basePathParts = [
            __DIR__,
            $additionalPath,
            'Fixtures',
            $className
        ];

        return implode(DIRECTORY_SEPARATOR, $basePathParts);
    }

    /**
     * Returns fixture file path by given file name.
     *
     * @param string $fixture Filename of fixture
     *
     * @return string Filepath to fixture
     */
    public function getFixtureFilePath(string $fixture): string
    {
        return $this->getFixturePath() . '/' . $fixture;
    }

    /**
     * Returns a list of files which start with Correct*
     *
     * @return string[] File list
     */
    public function getCorrectFileList(): array
    {
        $fixtureFiles = scandir($this->getFixturePath(), SCANDIR_SORT_NONE);

        $files = preg_grep('/^Correct(.*)\.(php)$/', $fixtureFiles);

        $providerFiles = [];

        foreach ($files as $file) {
            $providerFiles[$file][] = $file;
        }

        return $providerFiles;
    }

    /**
     * Checks the given file with defined error codes.
     *
     * @param string $file Filename of the fixture
     * @param array $sniffProperties Array of sniff properties
     *
     * @return File The php cs file
     */
    abstract protected function checkSniffFile(string $file, array $sniffProperties = []): File;
}
