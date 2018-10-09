<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use function explode;

/**
 * Class ThrowsTagSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class ThrowsTagSniff extends AbstractTagSniff
{
    /**
     * Code that the tag content format is invalid.
     *
     * @var string
     */
    public const CODE_TAG_MISSING_DESC_DESC = 'MissingThrowDescription';

    /**
     * Message that the tag content format is invalid.
     *
     * @var string
     */
    protected const MESSAGE_CODE_TAG_MISSING_DESC_DESC = 'Are you sure that you do not want to describe the throw?';

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

        if ($isNotValid = (count($returnParts) <= 1)) {
            $this->file->addWarning(
                static::MESSAGE_CODE_TAG_MISSING_DESC_DESC,
                $this->stackPos,
                static::CODE_TAG_MISSING_DESC_DESC
            );
        }

        $this->getFile()->recordMetric(
            $this->getStackPos(),
            sprintf('Valid %s tag:', $this->registerTag()),
            $isNotValid ? 'No' : 'Yes'
        );
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'throws';
    }
}
