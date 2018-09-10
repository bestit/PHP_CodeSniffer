<?php

declare(strict_types=1);

namespace BestIt;

use PHP_CodeSniffer\Files\File;
use ReflectionClass;
use ReflectionException;
use SlevomatCodingStandard\Sniffs\TestCase as SlevomatTestCase;
use function implode;
use const DIRECTORY_SEPARATOR;

/**
 * Class SniffTestCase
 *
 * @package BestIt\Sniffs
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
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
     * Checks the given file with defined error codes.
     *
     * @param string $file Filename of the fixture
     * @param array $sniffProperties Array of sniff properties
     *
     * @return File The php cs file
     */
    abstract protected function checkSniffFile(string $file, array $sniffProperties = []): File;
}
