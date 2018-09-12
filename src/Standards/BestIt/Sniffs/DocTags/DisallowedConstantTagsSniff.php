<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\ConstantRegistrationTrait;
use PHP_CodeSniffer\Util\Tokens;

/**
 * Sniff to disallow the given tags.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class DisallowedConstantTagsSniff extends AbstractDisallowedTagsSniff
{
    use ConstantRegistrationTrait;

    /**
     * This tags are disallowed and could be injected from the outside.
     *
     * @var array
     */
    public $disallowedTags = [
        'api',
        'author',
        'category',
        'copyright',
        'deprecated',
        'example',
        'filesource',
        'global',
        'ignore',
        'internal',
        'license',
        'link',
        'method',
        'package',
        'param',
        'property',
        'property-read',
        'property-write',
        'return',
        'since',
        'source',
        'subpackage',
        'throws',
        'todo',
        'uses',
        'version',
    ];
}
