<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\ClassHelper as BaseHelper;

/**
 * Helps you with classes.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class ClassHelper extends BaseHelper
{
    /**
     * Returns the positions for the uses in this class.
     *
     * @param File $file
     * @param int $classPos
     *
     * @return array
     */
    public static function getTraitUsePointers(File $file, int $classPos): array
    {
        $useStatements = [];
        $tokens = $file->getTokens();
        $classToken = $tokens[$classPos];
        $scopeLevel = $classToken['level'] + 1;

        for ($scopePos = $classToken['scope_opener'] + 1; $scopePos < $classToken['scope_closer']; $scopePos++) {
            $foundToken = $tokens[$scopePos];

            if (($foundToken['code'] === T_USE) && ($foundToken['level'] === $scopeLevel)) {
                $useStatements[] = $scopePos;
            }
        }

        return $useStatements;
    }
}
