<?php

declare(strict_types = 1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;

/**
 * Class PropertyHelper
 *
 * @package BestIt\Helper
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
final class PropertyHelper
{
    /**
     * The wrapped PHP_CodeSniffer_File
     *
     * @var File
     */
    private $file;

    /**
     * PropertyHelper constructor.
     *
     * @param File $file The wrapped PHP_CodeSniffer_File
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * Determines if the given variable is a property of a class.
     *
     * @param int $variablePtr Pointer to the current variable
     *
     * @return bool Indicator if the current T_VARIABLE is a property of a class
     */
    public function isProperty(int $variablePtr): bool
    {
        $tokens = $this->file->getTokens();

        $propertyPointer = $this->file->findPrevious(
            [T_STATIC, T_WHITESPACE, T_COMMENT],
            $variablePtr - 1,
            null,
            true
        );
        $propertyToken = $tokens[$propertyPointer];
        $propertyCode = $propertyToken['code'];

        return in_array(
            $propertyCode,
            [
                T_PRIVATE,
                T_PROTECTED,
                T_PUBLIC,
                T_VAR
            ],
            true
        );
    }
}
