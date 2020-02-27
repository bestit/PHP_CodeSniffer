<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\ClassRegistrationTrait;

/**
 * Sniffs the required tags of a class.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class RequiredClassTagsSniff extends AbstractRequiredTagsSniff
{
    use ClassRegistrationTrait;

    /**
     * Returns the minimum count for the packages.
     *
     * @return int
     */
    public function getMinimumForPackage(): int
    {
        $namespacePtr = $this->file->findNext(
            [T_NAMESPACE],
            0
        );

        return (int) ($namespacePtr !== false);
    }

    /**
     * Returns the required tag data.
     *
     * The order in which they appear in this array os the order for tags needed.
     *
     * @return array List of tag metadata
     */
    protected function getTagRules(): array
    {
        return [
            'package' => [
                'min' => [$this, 'getMinimumForPackage'],
                'max' => 1,
            ],
            'author' => [
                'min' => 1
            ],
            'version' => [
                'max' => 1
            ],
            'deprecated' => [
                'max' => 1
            ]
        ];
    }
}
