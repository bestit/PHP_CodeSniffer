<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

/**
 * Class PackageValidator
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Commenting\TagValidator\Validators
 */
class PackageValidator extends AbstractValidator
{
    /**
     * Returns the expected content for the tag.
     *
     * @return string The expected content
     */
    protected function getExpectedContent(): string
    {
        return 'Path\\To\\Your\\Package';
    }

    /**
     * Validates the content.
     *
     * @param string $content Tag content to be validated
     *
     * @return bool Indicator if content is valid or not
     */
    protected function validateContent(string $content): bool
    {
        if (strpos($content, ' ') !== false) {
            return false;
        }

        if (strpos($content, '\\') === 0) {
            return false;
        }

        $parts = explode('\\', $content);

        return (count($parts) === count(array_filter($parts)));
    }
}
