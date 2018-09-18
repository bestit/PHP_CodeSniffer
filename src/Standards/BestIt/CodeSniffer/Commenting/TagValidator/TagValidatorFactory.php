<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Commenting\TagValidator;

use BestIt\CodeSniffer\Commenting\TagValidator\Validators\ValidatorInterface;
use BestIt\CodeSniffer\File;
use function class_exists;
use function substr;
use function ucfirst;

/**
 * Class TagValidatorFactory
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Commenting\TagValidator
 */
class TagValidatorFactory
{
    /**
     * Creates the validator instance by given tag name.
     *
     * @param File $file The php cs file
     * @param string $tagName Name of the comment tag
     *
     * @return ValidatorInterface|null Validator or null
     */
    public function createFromTagName(File $file, string $tagName): ?ValidatorInterface
    {
        $validatorClass = 'BestIt\\CodeSniffer\\Commenting\\TagValidator\\Validators\\' .
            ucfirst(substr($tagName, 1)) . 'Validator';

        return (class_exists($validatorClass, true)) ? new $validatorClass($file) : null;
    }
}
