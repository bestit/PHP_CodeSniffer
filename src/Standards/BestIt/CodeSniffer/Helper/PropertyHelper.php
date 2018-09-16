<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;
use function substr;

/**
 * Class PropertyHelper
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Helper
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
     * Returns the names of the class' properties.
     *
     * @param array $classToken The token array for the class like structure.
     * @param File|null $file The used file. If not given then the file from the construct is used.
     *
     * @return array The names of the properties.
     */
    public function getProperties(array $classToken, ?File $file = null): array
    {
        if (!$file) {
            $file = $this->file;
        }

        $properties = [];
        $startPos = $classToken['scope_opener'] ?? 0;
        $tokens = $file->getTokens();

        while (($propertyPos = $file->findNext([T_VARIABLE], $startPos, $classToken['scope_closer'])) > 0) {
            if ($this->isProperty($propertyPos)) {
                $properties[] = substr($tokens[$propertyPos]['content'], 1);
            }

            $startPos = $propertyPos + 1;
        }

        return $properties;
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
