<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

use BestIt\Sniffs\Commenting\AbstractDocSniff;

/**
 * Class ParamValidator
 *
 * @package BestIt\Commenting\TagValidator\Validators
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ParamValidator extends AbstractValidator
{
    /**
     * Validates the tag content.
     *
     * @param array $tagToken Token data of the current token
     * @param array|null $contentToken Token of the tag content
     *
     * @return void
     */
    public function validate(array $tagToken, ?array $contentToken): void
    {
        parent::validate($tagToken, $contentToken);

        if ($contentToken) {
            $this->addWarnings($tagToken, $contentToken['content']);
        }
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

    /**
     * Adds warnings to the tag line
     *
     * @param array $tagToken Token data of the current token
     * @param string $content Tag content to be validated
     *
     * @return void
     */
    private function addWarnings(array $tagToken, string $content)
    {
        if (substr_count($content, 'mixed') >= 1) {
            $this->file->addWarningOnLine(
                AbstractDocSniff::MESSAGE_TAG_WARNING_MIXED,
                $tagToken['line'],
                AbstractDocSniff::CODE_TAG_WARNING_MIXED
            );
        }
    }
}
