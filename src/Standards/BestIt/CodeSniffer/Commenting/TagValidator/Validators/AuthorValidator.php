<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

/**
 * Class AuthorValidator
 *
 * @package BestIt\Commenting\TagValidator\Validators
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class AuthorValidator extends AbstractValidator
{
    /**
     * Returns the expected content for the tag.
     *
     * @return string The expected content
     */
    protected function getExpectedContent(): string
    {
        return 'Firstname Lastname <your.email@example.com>';
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
        $parts = explode(' ', $content);

        if (count($parts) < 3) {
            return false;
        }

        $email = array_pop($parts);

        if (strpos($email, '<') !== 0) {
            return false;
        }

        if (strpos($email, '>') !== strlen($email) - 1) {
            return false;
        }

        $rawEmail = str_replace(['<', '>'], '', $email);

        if (!filter_var($rawEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
