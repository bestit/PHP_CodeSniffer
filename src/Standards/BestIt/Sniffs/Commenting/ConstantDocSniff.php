<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

/**
 * Class ConstantDocSniff
 *
 * @package BestIt\Sniffs\Commenting
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ConstantDocSniff extends AbstractDocSniff
{
    /**
     * This tags are disallowed and could be injected from the outside.
     *
     * @var array
     */
    public $disallowedTags = [
        '@api',
        '@author',
        '@category',
        '@copyright',
        '@deprecated',
        '@example',
        '@filesource',
        '@global',
        '@ignore',
        '@internal',
        '@license',
        '@link',
        '@method',
        '@package',
        '@param',
        '@property',
        '@property-read',
        '@property-write',
        '@return',
        '@see',
        '@since',
        '@source',
        '@subpackage',
        '@throws',
        '@todo',
        '@uses',
        '@version',
    ];

    /**
     * Returns which tokens should be listened to.
     *
     * @return int[] List of tokens which should be listened to
     */
    public function getListenedTokens(): array
    {
        return [
            T_CONST
        ];
    }

    /**
     * Returns allowed tag metadata.
     *
     * The order in which they appear in this array os the order for tags needed.
     *
     * @return array List of tag metadata
     */
    public function getTagMetadata(): array
    {
        return [
            '@var' => [
                'min' => 1,
                'max' => 1
            ],
        ];
    }
}
