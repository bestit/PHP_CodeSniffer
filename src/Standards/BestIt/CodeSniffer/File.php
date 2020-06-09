<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer;

use PHP_CodeSniffer\Fixer;

/**
 * Class File
 *
 * Wrapper Class for PhpCsFile to provide a consistent way to replace int|bool returns
 * with int|bool returns false
 * Additionally there could be some architecture changes in the future, like Token-Objects and so on.
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\CodeSniffer
 */
class File extends AbstractFileDecorator
{
    /**
     * Returns the eol char of the file
     *
     * @return string Returns the EndOfLine-Character of the processed file
     */
    public function getEolChar(): string
    {
        return $this->getBaseFile()->eolChar;
    }

    /**
     * Returns the Wrapped PHP_CodeSniffer_Fixer
     *
     * @return Fixer Returns the fixer class.
     */
    public function getFixer(): Fixer
    {
        return $this->getBaseFile()->fixer;
    }
}
