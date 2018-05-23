<?php

declare(strict_types = 1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

/**
 * Class ThrowsValidator
 *
 * @package BestIt\Commenting\TagValidator\Validators
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ThrowsValidator extends AbstractValidator
{
    /**
     * Returns the expected content for the tag.
     *
     * @return string The expected content
     */
    protected function getExpectedContent(): string
    {
        return 'InvalidArgumentException Description why this exception is thrown';
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
        return substr_count($content, ' ') >= 1;
    }
}
