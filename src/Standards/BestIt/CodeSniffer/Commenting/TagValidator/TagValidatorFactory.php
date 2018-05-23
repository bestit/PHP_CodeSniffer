<?php

declare(strict_types = 1);

namespace BestIt\CodeSniffer\Commenting\TagValidator;

use BestIt\CodeSniffer\Commenting\TagValidator\Validators\AuthorValidator;
use BestIt\CodeSniffer\Commenting\TagValidator\Validators\DeprecatedValidator;
use BestIt\CodeSniffer\Commenting\TagValidator\Validators\PackageValidator;
use BestIt\CodeSniffer\Commenting\TagValidator\Validators\ParamValidator;
use BestIt\CodeSniffer\Commenting\TagValidator\Validators\ReturnValidator;
use BestIt\CodeSniffer\Commenting\TagValidator\Validators\ThrowsValidator;
use BestIt\CodeSniffer\Commenting\TagValidator\Validators\ValidatorInterface;
use BestIt\CodeSniffer\Commenting\TagValidator\Validators\VarValidator;
use BestIt\CodeSniffer\Commenting\TagValidator\Validators\VersionValidator;
use BestIt\CodeSniffer\File;

/**
 * Class TagValidatorFactory
 *
 * @package BestIt\Commenting\TagValidator
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
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
    public function createFromTagName(File $file, string $tagName)
    {
        $validator = null;

        switch ($tagName) {
            case '@return':
                $validator = new ReturnValidator($file);
                break;
            case '@param':
                $validator = new ParamValidator($file);
                break;
            case '@author':
                $validator = new AuthorValidator($file);
                break;
            case '@var':
                $validator = new VarValidator($file);
                break;
            case '@package':
                $validator = new PackageValidator($file);
                break;
            case '@version':
                $validator = new VersionValidator($file);
                break;
            case '@throws':
                $validator = new ThrowsValidator($file);
                break;
            case '@deprecated':
                $validator = new DeprecatedValidator($file);
                break;
        }

        return $validator;
    }
}
