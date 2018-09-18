<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

use function filter_var;
use function preg_match;
use const FILTER_VALIDATE_EMAIL;

/**
 * Class AuthorValidator
 *
 * @author blange <nick.lubisch@bestit-online.de>
 * @package BestIt\Commenting\TagValidator\Validators
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
        return 'name <your.email@example.com>';
    }

    /**
     * Validates the content.
     *
     * @param string $content Tag content to be validated
     *
     * @return bool Returns true if the content matches "name <your.email@example.com>" and contains a valid mail.
     */
    protected function validateContent(string $content): bool
    {
        $isValidContent = false;
        $matches = [];

        if (preg_match('/(?P<name>[\w\s]*)\s<(?P<mail>.*)>/uU', $content, $matches) === 1) {
            $isValidContent = (bool) filter_var($matches['mail'], FILTER_VALIDATE_EMAIL);
        }


        return $isValidContent;
    }
}
