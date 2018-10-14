<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use BestIt\CodeSniffer\File;

/**
 * Helps you with the new required file api.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait FileTrait
{
    /**
     * The used file.
     *
     * @var File|void
     */
    protected $file;

    /**
     * Type-safe getter for the file.
     *
     * @return File
     */
    protected function getFile(): File
    {
        return $this->file;
    }
}
