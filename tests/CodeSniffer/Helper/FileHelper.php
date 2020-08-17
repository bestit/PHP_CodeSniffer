<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;
use function file_get_contents;

/**
 * Helps the tests to load a code sniffer file.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class FileHelper
{
    /**
     * Fills a codesniffer file object for the given file path.
     *
     * @param string $filePath
     * @throws RuntimeException
     *
     * @return File
     */
    public function getFile(string $filePath): File
    {
        $file = new File(
            $filePath,
            new Ruleset($config = new Config()),
            $config,
        );

        $file->setContent(file_get_contents($filePath));
        $file->parse();

        return $file;
    }
}
