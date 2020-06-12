<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use PHP_CodeSniffer\Files\File;

/**
 * Registers the exception as an error or warning on the file.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class ExceptionHelper
{
    /**
     * The sniffed file.
     *
     * @var File
     */
    private File $file;

    /**
     * ExceptionHelper constructor.
     *
     * @param File $file The sniffed file.
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * Registers the exception as an error or warning on the file.
     *
     * @param CodeWarning $exception The error which should be handled.
     *
     * @return bool Should this error be fixed?
     */
    public function handleException(CodeWarning $exception): bool
    {
        $isError = $exception instanceof CodeError;
        $isFixable = $exception->isFixable();
        $method = 'add';

        if ($isFixable) {
            $method .= 'Fixable';
        }

        $method .= $isError ? 'Error' : 'Warning';

        return $this->file->$method(
            $exception->getMessage(),
            $exception->getStackPosition(),
            $exception->getCode(),
            $exception->getPayload()
        );
    }
}
