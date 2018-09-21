<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use PHP_CodeSniffer\Files\File;

/**
 * The basic calls for checking sniffs against files.
 *
 * @author Bjoern Lange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait DefaultSniffIntegrationTestTrait
{
    /**
     * Asserts all warnings in a given file.
     *
     * @param string $file Filename of the fixture
     * @param string $error Error code
     * @param int[] $lines Array of lines where the error code occurs
     *
     * @return File The php cs file
     */
    abstract protected function assertWarningsInFile(
        string $file,
        string $error,
        array $lines
    ): File;
}
