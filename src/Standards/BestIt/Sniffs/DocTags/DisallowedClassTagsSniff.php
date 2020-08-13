<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\ClassRegistrationTrait;

/**
 * Sniff to disallow the given tags.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class DisallowedClassTagsSniff extends AbstractDisallowedTagsSniff
{
    use ClassRegistrationTrait;

    /**
     * This tags are disallowed and could be injected from the outside.
     *
     * @var array
     */
    public $disallowedTags = [
        'api',
        'category',
        'copyright',
        'example',
        'filesource',
        'global',
        'ignore',
        'internal',
        'license',
        'param',
        'return',
        'since',
        'source',
        'subpackage',
        'throws',
        'todo',
        'uses',
        'var',
        'inheritdoc',
    ];
}
