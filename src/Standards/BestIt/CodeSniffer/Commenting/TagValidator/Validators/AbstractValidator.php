<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

use BestIt\CodeSniffer\File;
use BestIt\Sniffs\Commenting\AbstractDocSniff;

/**
 * Class AbstractValidator
 *
 * @package BestIt\Commenting\TagValidator\Validators
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * The php cs file.
     *
     * @var File
     */
    protected $file;

    /**
     * AbstractValidator constructor.
     *
     * @param File $file The php cs file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

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
        $result = false;

        if ($contentPtr !== -1) {
            $content = $contentToken['content'];
            $result = $this->validateContent($content);
        }

        if (!$result) {
            $this->addInvalidFormatError($tagToken, $this->getExpectedContent());
        }
    }

    /**
     * Adds the invalid format error.
     *
     * @param array $tagToken Token data of the current tag
     * @param string $expected Description what is expected
     *
     * @return void
     */
    public function addInvalidFormatError(array $tagToken, string $expected)
    {
        $this->file->addError(
            AbstractDocSniff::MESSAGE_TAG_CONTENT_FORMAT_INVALID,
            $tagToken['pointer'],
            AbstractDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
            [
                $tagToken['content'],
                $expected
            ]
        );
    }

    /**
     * Validates the content.
     *
     * @param string $content Tag content to be validated
     *
     * @return bool Indicator if content is valid or not
     */
    abstract protected function validateContent(string $content): bool;

    /**
     * Returns the expected content for the tag.
     *
     * @return string The expected content
     */
    abstract  protected function getExpectedContent(): string;
}
