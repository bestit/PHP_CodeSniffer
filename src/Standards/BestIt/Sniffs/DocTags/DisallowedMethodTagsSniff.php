<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\FunctionRegistrationTrait;

/**
 * Class DisallowedMethodTagsSniff.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class DisallowedMethodTagsSniff extends AbstractDisallowedTagsSniff
{
    use FunctionRegistrationTrait;

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
        'filesource',
        'global',
        'ignore',
        'internal',
        'license',
        'link',
        'method',
        'package',
        'property',
        'property-read',
        'property-write',
        'since',
        'source',
        'subpackage',
        'todo',
        'uses',
        'var',
        'version',
        'inheritdoc'
    ];
}
