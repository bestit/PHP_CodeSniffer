<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

/**
 * Checks the deprecated tag.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class DeprecatedTagSniff extends AbstractTagSniff
{
    use TagContentFormatTrait;

    /**
     * The error code for the missing dates.
     */
    public const CODE_TAG_MISSING_DATES = 'MissingDates';

    /**
     * The message for the error.
     */
    private const MESSAGE_TAG_MISSING_DATES = 'Please provide the version since when its deprecated and when it will ' .
        'be removed (Pattern: %s).';

    /**
     * Returns the payload for the error or warning registration.
     *
     * @param null|string $tagContent The content of the tag.
     *
     * @return array|void
     */
    protected function getReportData(?string $tagContent = null): ?array
    {
        // Satisfy php md
        unset($tagContent);

        return [
            self::MESSAGE_TAG_MISSING_DATES,
            $this->stackPos,
            self::CODE_TAG_MISSING_DATES,
            [
                $this->getValidPattern()
            ]
        ];
    }

    /**
     * Returns a pattern to check if the content is valid.
     *
     * @return string The pattern which matches successful.
     */
    protected function getValidPattern(): string
    {
        return '/since (version )?(?P<since>[\d\.]+)\. To be removed in (version )?(?P<target>[\d\.]+)($|\.|.*)/m';
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'deprecated';
    }
}
