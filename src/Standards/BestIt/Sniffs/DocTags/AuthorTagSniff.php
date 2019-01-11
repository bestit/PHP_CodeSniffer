<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

/**
 * Checks if the content of the author tag is valid.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class AuthorTagSniff extends AbstractTagSniff
{
    use TagContentFormatTrait {
        isValidContent as protected isValidContentInTrait;
    }

    /**
     * You MUST commit to your codes and give an [author tag](http://docs.phpdoc.org/references/phpdoc/tags/author.html).
     */
    const CODE_TAG_CONTENT_FORMAT_INVALID = 'TagContentFormatInvalid';

    /**
     * Returns the payload for the error or warning registration.
     *
     * @param null|string $tagContent The content of the tag.
     *
     * @return array|void
     */
    protected function getReportData(string $tagContent = null)
    {
        return [
            static::MESSAGE_TAG_CONTENT_FORMAT_INVALID,
            $this->stackPos,
            static::CODE_TAG_CONTENT_FORMAT_INVALID,
            [
                $tagContent,
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
        return '/(?P<name>[\w\s]*)\s<(?P<mail>.*)>/uU';
    }

    /**
     * Returns true if the given content matches the valid pattern and the given callback returns.
     *
     * @param null|string $tagContent The content of the tag.
     * @param callable|null $callback An additional callable after the pattern matches.
     *
     * @return bool True if the given content matches the valid pattern and the given callback returns.
     */
    protected function isValidContent(string $tagContent = null, callable $callback = null): bool
    {
        // Satisfy php md
        unset($callback);

        return $this->isValidContentInTrait($tagContent, function (array $matches): bool {
            return (bool) filter_var($matches['mail'], FILTER_VALIDATE_EMAIL);
        });
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'author';
    }
}
