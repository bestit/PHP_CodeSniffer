<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper as BaseHelper;

/**
 * Proxies the slevomat class to get larger compatibility with older versions.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class TokenHelper extends BaseHelper
{
    /**
     * Returns all positions for the given types till the end position.
     *
     * @param File $phpcsFile
     * @param string|int|array $types
     * @param int $startPos
     * @param int|null $endPos
     *
     * @return int[]
     */
    public static function findNextAll(File $phpcsFile, $types, int $startPos, int $endPos = null): array
    {
        $pointers = [];

        while (($foundPos = self::findNext($phpcsFile, $types, $startPos, $endPos)) !== null) {
            $pointers[] = $foundPos;

            $startPos = $foundPos + 1;
        }

        return $pointers;
    }

    /**
     * Returns the position of the previous content.
     *
     * @param File $phpcsFile
     * @param string|int|array $types
     * @param string $content
     * @param int $startPointer
     * @param int|null $endPointer
     *
     * @return int|null
     */
    public static function findPreviousContent(
        File $phpcsFile,
        $types,
        string $content,
        int $startPointer,
        int $endPointer = null
    ) {
        $token = $phpcsFile->findPrevious($types, $startPointer, $endPointer, false, $content);

        return $token === false ? null : $token;
    }
}
