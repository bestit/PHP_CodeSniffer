<?php

declare(strict_types = 1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

use BestIt\Sniffs\Commenting\AbstractDocSniff;

/**
 * Class ReturnValidator
 *
 * @package BestIt\Commenting\TagValidator\Validators
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ReturnValidator extends AbstractValidator
{
    /**
     * Validates the tag content.
     *
     * @param array $tagToken Token data of the current token
     * @param int $contentPtr Pointer to the tag content
     * @param array|null $contentToken Token of the tag content
     *
     * @return void
     */
    public function validate(array $tagToken, int $contentPtr, $contentToken)
    {
        parent::validate($tagToken, $contentPtr, $contentToken);

        if ($contentPtr !== -1) {
            $this->addWarnings($tagToken, $contentToken['content']);
        }
    }

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
     *
     * @return bool Indicator if content is valid or not
     */
    protected function validateContent(string $content): bool
    {
        if ($content === 'void') {
            return true;
        }

        if (substr_count($content, ' ') >= 1) {
            return true;
        }

        return false;
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
