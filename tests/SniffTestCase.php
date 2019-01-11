<?php

declare(strict_types=1);

namespace BestIt;

use Exception;
use PHP_CodeSniffer\Files\File;
use ReflectionClass;
use ReflectionException;
use SlevomatCodingStandard\Sniffs\TestCase as SlevomatTestCase;
use function dirname;
use function implode;
use const DIRECTORY_SEPARATOR;

/**
 * The basic sniff test case.
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt
 */
abstract class SniffTestCase extends SlevomatTestCase
{
    /**
     * The cached folder path for the fixtures of this class.
     *
     * @var string
     */
    private $fixturePath;

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
        if ((!$dirname = dirname($file)) || ($dirname === '.')) {
            $file = $this->getFixtureFilePath($file);
        }

        $report = $this->checkFile($file, $sniffProperties);

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
     * Asserts all warnings in a given file.
     *
     * @throws Exception
     *
     * @param string $file Filename of the fixture
     * @param string $warning Code of the warning
     * @param int[] $lines Array of lines where the error code occurs
     * @param array $sniffProperties Array of sniff properties
     *
     * @return File The php cs file
     */
    protected function assertWarningsInFile(
        string $file,
        string $warning,
        array $lines,
        array $sniffProperties = []
    ): File {
        $report = $this->checkFile($file, $sniffProperties);

        foreach ($lines as $line) {
            $this->assertSniffWarning(
                $report,
                $line,
                $warning
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
        $report = $this->checkFile($file);

        $this->assertNoSniffErrorInFile($report);
        $this->assertNoSniffWarningInFile($report);
    }

    /**
     * Processes the fixture path.
     *
     * @return string Fixture path
     */
    protected function getFixturePath(): string
    {
        if (!$this->fixturePath) {
            $this->fixturePath = $this->loadFixturePath();
        }

        return $this->fixturePath;
    }

    /**
     * Returns fixture file path by given file name.
     *
     * @param string $fixture Filename of fixture
     *
     * @return string Filepath to fixture
     */
    protected function getFixtureFilePath(string $fixture): string
    {
        return $this->getFixturePath() . '/' . $fixture;
    }

    /**
     * Returns a list of files which start with correct*
     *
     * @return array With the path to a file as the first parameter.
     */
    public function getCorrectFileList(): array
    {
        $providerFiles = [];

        foreach (glob($this->getFixturePath() . DIRECTORY_SEPARATOR . 'Correct*.php') as $file) {
            $providerFiles[basename($file)] = [$file];
        }

        return $providerFiles;
    }

    /**
     * Returns the path to the fixture folder for this sniff.
     *
     * @return string The path to the fixture folder for this sniff.
     */
    protected function loadFixturePath(): string
    {
        $basePathParts = [];

        try {
            $reflection = new ReflectionClass(static::class);

            $basePathParts = [
                dirname($reflection->getFileName()),
                'Fixtures',
                substr($reflection->getShortName(), 0, -4)
            ];
        } catch (ReflectionException $e) {
            // Do nothing, this class exists!
        }

        return implode(DIRECTORY_SEPARATOR, $basePathParts);
    }

    /**
     * Checks that there are no warnings for the given file.
     *
     * Copied the following from slevomat but changed to warnings.
     *
     * @param File $file
     * @see SlevomatTestCase::assertNoSniffErrorInFile()
     *
     * @return void
     */
    protected static function assertNoSniffWarningInFile(File $file)
    {
        $warnings = $file->getWarnings();

        self::assertEmpty($warnings, sprintf('No warnings expected, but %d warnings found.', count($warnings)));
    }

    /**
     * Checks the warnings for the given sniff file.
     *
     * Copied the following from slevomat but changed to warnings.
     *
     * @see SlevomatTestCase::assertSniffError()
     *
     * @param File $codeSnifferFile
     * @param int $line
     * @param string $code
     * @param null|string $message
     *
     * @return void
     */
    protected static function assertSniffWarning(
        File $codeSnifferFile,
        int $line,
        string $code,
        $message = null
    ) {
        $warnings = $codeSnifferFile->getWarnings();
        self::assertTrue(isset($warnings[$line]), sprintf('Expected warning on line %s, but none found.', $line));

        $sniffCode = sprintf('%s.%s', static::getSniffName(), $code);

        self::assertTrue(
            self::hasWarning($warnings[$line], $sniffCode, $message),
            sprintf(
                'Expected warning %s%s, but none found on line %d.%sWarnings found on line %d:%s%s%s',
                $sniffCode,
                $message !== null ? sprintf(' with message "%s"', $message) : '',
                $line,
                PHP_EOL . PHP_EOL,
                $line,
                PHP_EOL,
                self::getFormattedWarnings($warnings[$line]),
                PHP_EOL
            )
        );
    }

    /**
     * Copied from slevomat but changed to warnings.
     *
     * @see SlevomatTestCase::assertNoSniffError()
     *
     * @param File $codeSnifferFile
     * @param int $line
     *
     * @return void
     */
    protected static function assertNoSniffWarning(File $codeSnifferFile, int $line)
    {
        $warnings = $codeSnifferFile->getWarnings();
        self::assertFalse(
            isset($warnings[$line]),
            sprintf(
                'Expected no warning on line %s, but found:%s%s%s',
                $line,
                PHP_EOL . PHP_EOL,
                isset($warnings[$line]) ? self::getFormattedWarnings($warnings[$line]) : '',
                PHP_EOL
            )
        );
    }

    /**
     * Copied from slevomat but changed to warnings.
     *
     * @see SlevomatTestCase::hasError()
     *
     * @param mixed[][][] $warningsOnLine
     * @param string $sniffCode
     * @param string|null $message
     *
     * @return bool
     */
    private static function hasWarning(array $warningsOnLine, string $sniffCode, $message = null): bool
    {
        foreach ($warningsOnLine as $warningsOnPosition) {
            foreach ($warningsOnPosition as $warning) {
                if (!(
                    $warning['source'] === $sniffCode
                    && ($message === null || strpos($warning['message'], $message) !== false)
                )) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Copied from slevomat but changed to warnings.
     *
     * @param array $warnings
     * @see SlevomatTestCase::getFormattedErrors()
     *
     * @return string
     */
    private static function getFormattedWarnings(array $warnings): string
    {
        return implode(PHP_EOL, array_map(function (array $warnings): string {
            return implode(PHP_EOL, array_map(function (array $warning): string {
                return sprintf("\t%s: %s", $warning['source'], $warning['message']);
            }, $warnings));
        }, $warnings));
    }
}
