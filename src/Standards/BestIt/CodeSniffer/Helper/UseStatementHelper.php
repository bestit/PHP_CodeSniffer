<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper as BaseHelper;
use function is_callable;
use function method_exists;

/**
 * Helper for use statements.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class UseStatementHelper extends BaseHelper
{
    /**
     * Searches for the use statement type in the given file.
     *
     * @param File $file
     * @param UseStatement $useStatement
     *
     * @return string
     */
    private static function findType(File $file, UseStatement $useStatement): string
    {
        $nextTokenFromUsePointer = TokenHelper::findNextEffective($file, $useStatement->getPointer() + 1);
        $tokens = $file->getTokens();
        $type = UseStatement::TYPE_DEFAULT;

        if ($tokens[$nextTokenFromUsePointer]['code'] === T_STRING) {
            if ($tokens[$nextTokenFromUsePointer]['content'] === 'const') {
                $type = UseStatement::TYPE_CONSTANT;
            } elseif ($tokens[$nextTokenFromUsePointer]['content'] === 'function') {
                $type = UseStatement::TYPE_FUNCTION;
            }
        }

        return $type;
    }

    /**
     * Returns the type for the given use statement.
     *
     * @param File $file
     * @param UseStatement $useStatement
     *
     * @return string
     */
    public static function getType(File $file, UseStatement $useStatement): string
    {
        if (method_exists($useStatement, 'getType')) {
            $type = $useStatement->getType();
        } else {
            $type = self::findType($file, $useStatement);
        }

        return $type;
    }

    /**
     * Returns the type name for the given use statement.
     *
     * @param File $file
     * @param UseStatement $useStatement
     *
     * @return string
     */
    public static function getTypeName(File $file, UseStatement $useStatement): string
    {
        $type = static::getType($file, $useStatement);

        if (is_callable('\\SlevomatCodingStandard\\Helpers\\UseStatement::getTypeName')) {
            $typeName = UseStatement::getTypeName($type);
        } else {
            $names = [
                UseStatement::TYPE_CONSTANT => 'const',
                UseStatement::TYPE_DEFAULT => '',
                UseStatement::TYPE_FUNCTION => 'function'
            ];

            $typeName = $names[$type];
        }

        return $typeName ?? '';
    }
}
