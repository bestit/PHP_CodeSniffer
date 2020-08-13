<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\VariableRegistrationTrait;

/**
 * Sniffs the required tags of properties.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class RequiredPropertyTagsSniff extends AbstractRequiredTagsSniff
{
    use VariableRegistrationTrait;

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
            'var' => [
                'min' => 1,
                'max' => 1,
            ],
        ];
    }
}
