<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

/**
 * Class DeprecatedValidator
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Commenting\TagValidator\Validators
 */
class DeprecatedValidator extends AbstractValidator
{
    /**
     * Returns the expected content for the tag.
     *
     * @return string The expected content
     */
    protected function getExpectedContent(): string
    {
        return 'since version 1.0, to be removed in 2.0. [Use XYZ instead.]';
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
        return strlen($content) > 0;
    }
}
