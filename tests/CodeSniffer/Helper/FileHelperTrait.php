<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;

/**
 * Helps the tests to load a code sniffer file.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
trait FileHelperTrait
{
    /**
     * The used file for testing.
     *
     * @var File|null
     */
    protected ?File $file = null;

    /**
     * Fills a codesniffer file object for the given file path.
     *
     * @param string $filePath
     * @throws RuntimeException
     *
     * @return File
     */
    protected function getFile(string $filePath): File
    {
        return (new FileHelper())->getFile($filePath);
    }
}
