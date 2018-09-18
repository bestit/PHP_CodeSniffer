<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

/**
 * Class MethodDocSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class MethodDocSniff extends AbstractDocSniff
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
        '@filesource',
        '@global',
        '@ignore',
        '@internal',
        '@license',
        '@link',
        '@method',
        '@package',
        '@property',
        '@property-read',
        '@property-write',
        '@since',
        '@source',
        '@subpackage',
        '@todo',
        '@uses',
        '@var',
        '@version',
        '@inheritdoc'
    ];

    /**
     * Returns which tokens should be listened to.
     *
     * @return int[] List of tokens which is to be listened to
     */
    public function getListenedTokens(): array
    {
        return [
            T_FUNCTION
        ];
    }

    /**
     * Returns allowed tag metadata.
     *
     * @return array List of tag metadata
     */
    public function getTagMetadata(): array
    {
        return [
            '@param' => [
                'min' => 0,
                'max' => null
            ],
            '@return' => [
                'min' => 1,
                'max' => 1,
                'if' => [$this->getTagHelper(), 'isNoWhitelistedFunction']
            ],
            '@throws' => [
                'min' => 0,
                'max' => null
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
