<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use function explode;
use function in_array;

/**
 * Class ReturnTagSniff
 *
 * @auhor blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class ReturnTagSniff extends AbstractTagSniff
{
    /**
     * Code that the tag content format is invalid.
     *
     * @var string
     */
    public const CODE_TAG_MISSING_RETURN_DESC = 'MissingReturnDescription';

    /**
     * Message that the tag content format is invalid.
     *
     * @var string
     */
    protected const MESSAGE_CODE_TAG_MISSING_RETURN_DESC = 'Are you sure that you do not want to describe your return?';

    /**
     * This return types will not need a summary in any case.
     *
     * @var array
     */
    public $excludedTypes = ['void'];

    /**
     * Processed the content of the required tag.
     *
     * @param null|string $tagContent The possible tag content or null.
     *
     * @return void
     */
    protected function processTagContent(?string $tagContent = null): void
    {
        $returnParts = explode(' ', (string) $tagContent);

        if (!in_array($returnParts[0], $this->excludedTypes) && count($returnParts) <= 1) {
            $this->file->addWarning(
                static::MESSAGE_CODE_TAG_MISSING_RETURN_DESC,
                $this->stackPos,
                static::CODE_TAG_MISSING_RETURN_DESC
            );
        }
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'return';
    }
}
