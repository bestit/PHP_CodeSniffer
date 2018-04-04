<?php

declare(strict_types=1);

namespace Tests\BestIt;

use PHP_CodeSniffer_File;
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
     * @param PHP_CodeSniffer_File $codeSnifferFile The CodeSniffer file
     *
     * @return void
     */
    protected function assertAllFixedInFile(PHP_CodeSniffer_File $codeSnifferFile)
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
     * @return PHP_CodeSniffer_File The php cs file
     */
    protected function assertErrorsInFile(
        string $file,
        string $error,
        array $lines,
        array $sniffProperties = []
    ): PHP_CodeSniffer_File {
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
     * Asserts all errors in a given file.
     *
     * @param string $file Filename of the fixture
     * @param string $error Error code
     * @param int[] $lines Array of lines where the error code occurs
     * @param array $sniffProperties Array of sniff properties
     *
     * @return PHP_CodeSniffer_File The php cs file
     */
    protected function assertWarningsInFile(
        string $file,
        string $error,
        array $lines,
        array $sniffProperties = []
    ): PHP_CodeSniffer_File {
        $report = $this->checkSniffFile(
            $this->getFixtureFilePath($file),
            $sniffProperties
        );

        foreach ($lines as $line) {
            $this->assertSniffWarnings(
                $report,
                $line,
                $error
            );
        }

        return $report;
    }

    /**
     * Checks if the given file has warnings on the given line
     *
     * @param PHP_CodeSniffer_File $codeSnifferFile The code sniffer file class
     * @param int $line The line where a warning may occur
     * @param string $code The error code that might be thrown
     * @param string|null $message The message
     *
     * @return void
     */
    protected function assertSniffWarnings(
        PHP_CodeSniffer_File $codeSnifferFile,
        int $line,
        string $code,
        string $message = null
    ): void {
        $errors = $codeSnifferFile->getWarnings();
        $this->assertTrue(isset($errors[$line]), sprintf('Expected error on line %s, but none found.', $line));

        $sniffCode = sprintf('%s.%s', $this->getSniffName(), $code);

        $this->assertTrue(
            $this->hasWarning($errors[$line], $sniffCode, $message),
            sprintf(
                'Expected error %s%s, but none found on line %d.%sErrors found on line %d:%s%s%s',
                $sniffCode,
                $message !== null ? sprintf(' with message "%s"', $message) : '',
                $line,
                PHP_EOL . PHP_EOL,
                $line,
                PHP_EOL,
                $this->getFormattedWarnings($errors[$line]),
                PHP_EOL
            )
        );
    }

    /**
     * Checks if the file has a warning
     *
     * @param array $warningOnLine An array containing warnings
     * @param string $sniffCode The expected warning
     * @param string|null $message The expected message
     *
     * @return bool Returns true if the given array has the given warning
     */
    private function hasWarning(array $warningOnLine, string $sniffCode, string $message = null): bool
    {
        foreach ($warningOnLine as $warningsOnPosition) {
            foreach ($warningsOnPosition as $warning) {
                if ($warning['source'] === $sniffCode
                    && ($message === null || strpos($warning['message'], $message) !== false)
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Formats a warning
     *
     * @param array $warnings The unformatted warning
     *
     * @return string the formatted warning
     */
    private function getFormattedWarnings(array $warnings): string
    {
        return implode(PHP_EOL, array_map(function (array $errors): string {
            return implode(PHP_EOL, array_map(function (array $error): string {
                return sprintf("\t%s: %s", $error['source'], $error['message']);
            }, $errors));
        }, $warnings));
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
     * @return PHP_CodeSniffer_File The php cs file
     */
    abstract protected function checkSniffFile(string $file, array $sniffProperties = []): PHP_CodeSniffer_File;
}
