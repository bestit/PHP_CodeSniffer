<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

/**
 * Class ClassDocSniff
 *
 * @package BestIt\Sniffs\Commenting
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ClassDocSniff extends AbstractDocSniff
{
    /**
     * This tags are disallowed and could be injected from the outside.
     *
     * @var array
     */
    public $disallowedTags = [
        '@api',
        '@category',
        '@copyright',
        '@example',
        '@filesource',
        '@global',
        '@ignore',
        '@internal',
        '@license',
        '@method',
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
        '@var',
        '@inheritdoc'
    ];

    /**
     * Returns which tokens should be listened to.
     *
     * @return int[] Tokens which should be listened to
     */
    public function getListenedTokens(): array
    {
        return [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT
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
            '@package' => [
                'min' => 1,
                'max' => 1,
                'if' => [$this->getTagHelper(), 'hasNamespace']
            ],
            '@author' => [
                'min' => 1,
                'max' => null
            ],
            '@version' => [
                'min' => 0,
                'max' => 1
            ],
            '@deprecated' => [
                'min' => 0,
                'max' => 1
            ],
            '@link' => [
                'min' => 0,
                'max' => null
            ]
        ];
    }
}
