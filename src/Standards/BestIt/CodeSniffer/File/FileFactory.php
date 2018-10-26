<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\File;

use BestIt\CodeSniffer\File;
use PHP_CodeSniffer\Files\File as BaseFile;

/**
 * Caches the new generation of our decorators.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\File
 */
class FileFactory
{
    /**
     * The singleton instance.
     *
     * @var null|self
     */
    private static $instance;

    /**
     * The last used file in the code sniffer.
     *
     * @var null|File
     */
    private $lastFile = null;

    /**
     * FileFactory constructor.
     *
     * Protected to force the singleton.
     */
    protected function __construct()
    {
        // hide the constructor.
    }

    /**
     * Returns a decorated file for our system.
     *
     * @param BaseFile $baseFile
     *
     * @return File
     */
    public function getFile(BaseFile $baseFile): File
    {
        if ($this->isNewFileNeeded($baseFile)) {
            $this->lastFile = new File($baseFile);
        }

        return $this->lastFile;
    }

    /**
     * Singleton getter.
     *
     * @return FileFactory
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            static::setInstance(new FileFactory());
        }

        return self::$instance;
    }

    /**
     * Returns true if a new file decorator is needed.
     *
     * @param BaseFile $baseFile
     *
     * @return bool
     */
    private function isNewFileNeeded(BaseFile $baseFile): bool
    {
        return !$this->lastFile || $this->lastFile->getFilename() !== $baseFile->getFilename();
    }

    /**
     * Setter for the singleton instance.
     *
     * @param FileFactory $factory
     *
     * @return FileFactory
     */
    public static function setInstance(FileFactory $factory): self
    {
        return self::$instance = $factory;
    }
}
