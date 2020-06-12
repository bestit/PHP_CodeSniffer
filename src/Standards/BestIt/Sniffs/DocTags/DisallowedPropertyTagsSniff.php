<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\VariableRegistrationTrait;

/**
 * Sniff to disallow the given tags.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class DisallowedPropertyTagsSniff extends AbstractDisallowedTagsSniff
{
    use VariableRegistrationTrait;

    /**
     * This tags are disallowed and could be injected from the outside.
     *
     * @var array
     */
    public array $disallowedTags = [
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
