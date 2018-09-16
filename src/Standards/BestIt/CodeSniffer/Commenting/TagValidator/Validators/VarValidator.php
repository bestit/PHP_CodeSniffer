<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

/**
 * Class VarValidator
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Commenting\TagValidator\Validators
 */
class VarValidator extends AbstractValidator
{
    /**
     * Returns the expected content for the tag.
     *
     * @return string The expected content
     */
    protected function getExpectedContent(): string
    {
        return 'type[|type2[|...]]';
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
        return strpos($content, ' ') !== 0;
    }
}
