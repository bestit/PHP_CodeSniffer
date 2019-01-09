<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\Helper\TokenHelper;
use function array_column;
use function array_map;
use function implode;
use const T_FUNCTION;

/**
 * Sniffs the var tags.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class VarTagSniff extends AbstractTagSniff
{
    use TagContentFormatTrait;

    /**
     * You MUST provide a type for your var tag.
     */
    public const CODE_TAG_CONTENT_FORMAT_INVALID = 'TagContentFormatInvalid';


    /**
     * Returns a pattern to check if the content is valid.
     *
     * @return string The pattern which matches successful.
     */
    protected function getValidPattern(): string
    {
        return '/^(?P<type>[\w\|\[\]]+)(?P<var> \$\w+)?$|\s/';
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'var';
    }
}
