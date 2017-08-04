<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

/**
 * Class MethodDocSniff
 *
 * @package BestIt\Sniffs\Commenting
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class MethodDocSniff extends AbstractDocSniff
{
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
                'max' => null,
                'lineAfter' => true
            ],
            '@return' => [
                'min' => 1,
                'max' => 1,
                'if' => [$this->getTagHelper(), 'isNoWhitelistedFunction'],
                'lineAfter' => true
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

    /**
     * Returns an array of disallowed tokens.
     *
     * @return array List of disallowed tags
     */
    public function getDisallowedTags(): array
    {
        return [
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
            '@see',
            '@since',
            '@source',
            '@subpackage',
            '@todo',
            '@uses',
            '@var',
            '@version',
            '@inheritdoc'
        ];
    }
}
