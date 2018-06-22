<?php

declare(strict_types = 1);

namespace BestIt\CodeSniffer\Commenting\TagValidator\Validators;

/**
 * Interface ValidatorInterface
 *
 * @package BestIt\Commenting\TagValidator\Validators
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
interface ValidatorInterface
{
    /**
     * Validates the tag content.
     *
     * @param array $tagToken Token data of the current token
     * @param int|null $contentPtr Pointer to the tag content
     * @param array $contentToken Token of the tag content
     *
     * @return void
     */
    public function validate(array $tagToken, int $contentPtr, $contentToken);
}
