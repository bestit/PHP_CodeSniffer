<?php

declare(strict_types = 1);

namespace BestIt\CodeSniffer;

use PHP_CodeSniffer\Fixer as BaseFixer;
use PHP_CodeSniffer_Fixer;

/**
 * Class Fixer
 *
 * @package BestIt\CodeSniffer
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class Fixer
{
    /**
     * The PHP_CodeSniffer_Fixer
     *
     * @var BaseFixer
     */
    private $baseFixer;

    /**
     * The wrapped PHP_CodeSniffer_File
     *
     * @var File
     */
    private $file;

    /**
     * Fixer constructor.
     *
     * @param File $file The wrapped PHP_CodeSniffer_File
     * @param BaseFixer $baseFixer The PHP_CodeSniffer_Fixer
     */
    public function __construct(File $file, BaseFixer $baseFixer)
    {
        $this->file = $file;
        $this->baseFixer = $baseFixer;
    }

    /**
     * Start recording actions for a changeset.
     *
     * @return void
     */
    public function beginChangeset()
    {
        $this->baseFixer->beginChangeset();
    }

    /**
     * Replace the entire contents of a token.
     *
     * @param int $stackPtr The position of the token in the token stack.
     * @param string $content The new content of the token.
     *
     * @return bool If the change was accepted.
     */
    public function replaceToken(int $stackPtr, string $content): bool
    {
        return $this->baseFixer->replaceToken($stackPtr, $content);
    }

    /**
     * Adds content to the start of a token's current content.
     *
     * @param int $stackPtr The position of the token in the token stack.
     * @param string $content The content to add.
     *
     * @return bool If the change was accepted.
     */
    public function addContentBefore(int $stackPtr, string $content): bool
    {
        return $this->baseFixer->addContentBefore($stackPtr, $content);
    }

    /**
     * Adds content to the end of a token's current content.
     *
     * @param int $stackPtr The position of the token in the token stack.
     * @param string $content The content to add.
     *
     * @return bool If the change was accepted.
     */
    public function addContent(int $stackPtr, string $content): bool
    {
        return $this->baseFixer->addContent($stackPtr, $content);
    }

    /**
     * Stop recording actions for a changeset, and apply logged changes.
     *
     * @return bool Returns false if the changeset is in conflict
     */
    public function endChangeset(): bool
    {
        return $this->baseFixer->endChangeset() ?? true;
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

            $this->baseFixer->replaceToken($tagPtr, '');
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
