<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

/**
 * Class ReturnValidator
 *
 * @package BestIt\Commenting\TagValidator\Validators
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ReturnValidator extends AbstractValidator
{
    /**
     * Returns the expected content for the tag.
     *
     * @return string The expected content
     */
    protected function getExpectedContent(): string
    {
        return 'type[|type2[|...]] Return description';
    }

    /**
     * Validates the content.
     *
     * @param string $content Tag content to be validated
     * @param array $tagToken The tag token
     *
     * @return bool Indicator if content is valid or not
     */
    protected function validateContent(string $content, array $tagToken): bool
    {
        if ($content === 'void') {
            return true;
        }

        if (substr_count($content, ' ') >= 1) {
            return true;
        }

        return false;
    }
}
