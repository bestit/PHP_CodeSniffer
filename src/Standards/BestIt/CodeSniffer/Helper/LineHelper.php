<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File as FileDecorator;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Fixer;

/**
 * Helps you handling lines.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class LineHelper
{
    /**
     * The used file.
     *
     * @var File
     */
    private $file;

    /**
     * Dou you want to reuse a fixer?
     *
     * @var Fixer
     */
    private $fixer;

    /**
     * LineHelper constructor.
     *
     * @param File $file The used file.
     * @param Fixer|null $fixer Dou you want to reuse a fixer?
     */
    public function __construct(File $file, Fixer $fixer = null)
    {
        $this->file = $file;

        if (!$fixer) {
            $fixer = $file instanceof FileDecorator ? $file->getFixer() : $file->fixer;
        }

        $this->fixer = $fixer;
    }

    /**
     * Removes the given line.
     *
     * @param int $line The line which is to be removed
     *
     * @return void
     */
    public function removeLine(int $line)
    {
        foreach ($this->file->getTokens() as $tagPtr => $tagToken) {
            if ($tagToken['line'] !== $line) {
                continue;
            }

            $this->fixer->replaceToken($tagPtr, '');

            if ($tagToken['line'] > $line) {
                break;
            }
        }
    }

    /**
     * Removes lines by given start and end.
     *
     * @param int $startLine The first line which is to be removed
     * @param int $endLine The last line which is to be removed
     *
     * @return void
     */
    public function removeLines(int $startLine, int $endLine)
    {
        for ($line = $startLine; $line <= $endLine; $line++) {
            $this->removeLine($line);
        }
    }
}
