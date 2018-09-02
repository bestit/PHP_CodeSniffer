<?php

declare(strict_types = 1);

namespace BestIt;

use const DIRECTORY_SEPARATOR;
use function implode;
use PHP_CodeSniffer\Files\File;
use ReflectionClass;
use SlevomatCodingStandard\Sniffs\TestCase as SlevomatTestCase;

/**
 * Class SniffTestCase
 *
 * @package BestIt\Sniffs
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
abstract class SniffTestCase extends SlevomatTestCase
{
    /**
     * @var string The cached folder path for the fixtures of this class.
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
    ): void {
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
    protected function assertFileCorrect(string $file): void
    {
        $report = $this->checkSniffFile($file);

        $this->assertNoSniffErrorInFile($report);
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
     * Returns a list of files which start with Correct*
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

    protected function loadFixturePath(): string
    {
        $reflection = new ReflectionClass(static::class);

        $basePathParts = [
            dirname($reflection->getFileName()),
            'Fixtures',
            substr($reflection->getShortName(), 0, -4)
        ];

        return implode(DIRECTORY_SEPARATOR, $basePathParts);
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
