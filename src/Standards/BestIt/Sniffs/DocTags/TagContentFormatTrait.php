<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use Closure;

/**
 * Helps you validating tag contents.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
trait TagContentFormatTrait
{
    /**
     * The invalid tag should lead to an error.
     *
     * @var bool
     */
    protected $asError = true;

    /**
     * The found matches or void.
     *
     * @var array
     */
    protected $matches;

    /**
     * The invalid tag should lead to an error.
     *
     * @return bool
     */
    protected function asError(): bool
    {
        return $this->asError;
    }

    /**
     * Returns the payload for the error or warning registration.
     *
     * @param null|string $tagContent The content of the tag.
     *
     * @return array|void
     */
    protected function getReportData(?string $tagContent = null): ?array
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
    abstract protected function getValidPattern(): string;

    /**
     * Returns true if the given content matches the valid pattern and the given callback returns.
     *
     * @param null|string $tagContent The content of the tag.
     * @param callable|null $callback An additional callable after the pattern matches.
     *
     * @return bool True if the given content matches the valid pattern and the given callback returns.
     */
    protected function isValidContent(?string $tagContent = null, ?callable $callback = null): bool
    {
        $isValidContent = false;

        if (preg_match($this->getValidPattern(), (string) $tagContent, $this->matches) === 1) {
            $isValidContent = true;

            if ($callback) {
                $isValidContent = Closure::fromCallable($callback)($this->matches);
            }
        }
        return $isValidContent;
    }

    /**
     * Processed the content of the required tag.
     *
     * @param null|string $tagContent The possible tag content or null.
     *
     * @return void
     */
    protected function processTagContent(?string $tagContent = null): void
    {
        if (!$this->isValidContent($tagContent)) {
            $this->file->{'add' . ($this->asError() ? 'Error' : 'Warning')}(...$this->getReportData($tagContent));
        }
    }
}