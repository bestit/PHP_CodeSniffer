<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

/**
 * Checks the version tag.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class VersionTagSniff extends AbstractTagSniff
{
    use TagContentFormatTrait;

    /**
     * If you provide a version tag, you MUST provide it in [semver 2.0-Format](https://semver.org) with Major.Minor.Patch-Version .
     */
    public const CODE_TAG_CONTENT_FORMAT_INVALID = 'TagContentFormatInvalid';

    /**
     * Returns a pattern to check if the content is valid.
     *
     * @return string The pattern which matches successful.
     */
    protected function getValidPattern(): string
    {
        return '/^\d+\.\d+\.\d+.*$/';
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'version';
    }
}
