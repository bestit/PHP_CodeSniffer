<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

/**
 * Class ParamValidator
 *
 * @package BestIt\Commenting\TagValidator\Validators
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ParamValidator extends AbstractValidator
{
    /**
     * Validates the content.
     *
     * @param string $content Tag content to be validated
     *
     * @return bool Indicator if content is valid or not
     */
    protected function validateContent(string $content): bool
    {
        $parts = explode(' ', $content);

        if (count($parts) < 3) {
            return false;
        }

        $variable = $parts[1];

        return strpos($variable, '$') === 0;
    }

    /**
     * Returns the expected content for the tag.
     *
     * @return string The expected content
     */
    protected function getExpectedContent(): string
    {
        return 'type[|type2[|...]] $variable Description of variable';
    }
}
