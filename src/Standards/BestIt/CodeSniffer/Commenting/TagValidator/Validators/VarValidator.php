<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

/**
 * Class VarValidator
 *
 * @package BestIt\Commenting\TagValidator\Validators
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
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
     * @param array $tagToken The tag token
     *
     * @return bool Indicator if content is valid or not
     */
    protected function validateContent(string $content, array $tagToken): bool
    {
        $content = preg_replace('# +#', ' ', $content);
        $parts = explode(' ', $content);

        if ($parts[0] === 'array') {
            $this->addInvalidFormatWarning($tagToken, 'Please check if you can typehint your array '
                . ($parts[1] ?? ''));
        }

        return strpos($content, ' ') !== 0;
    }
}
