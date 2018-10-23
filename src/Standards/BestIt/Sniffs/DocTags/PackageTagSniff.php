<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_DOC_COMMENT_STRING;

/**
 * Checks if the package contains the actual namespace.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class PackageTagSniff extends AbstractTagSniff
{
    /**
     * Code that the tag content format is invalid.
     */
    public const CODE_TAG_WRONG_PACKAGE = 'WrongPackage';

    /**
     * Message that the tag content format is invalid.
     */
    protected const MESSAGE_CODE_TAG_WRONG_PACKAGE = 'The package needs to match your real namespace: %s';

    /**
     * Fixes the wrong package and replaces it with the correct namespace.
     *
     * @param string $currentNamespace
     *
     * @return void
     */
    private function fixWrongPackage(string $currentNamespace): void
    {
        $this->file->getFixer()->replaceToken(
            TokenHelper::findNext($this->file->getBaseFile(), [T_DOC_COMMENT_STRING], $this->stackPos),
            ' ' . $currentNamespace
        );
    }

    /**
     * Processed the content of the required tag.
     *
     * @param null|string $tagContent The possible tag content or null.
     *
     * @return void
     */
    protected function processTagContent(?string $tagContent = null): void
    {
        $currentNamespace = NamespaceHelper::findCurrentNamespaceName($this->file, $this->stackPos);

        if ($currentNamespace && $tagContent !== $currentNamespace) {
            $isFixing = $this->file->addFixableError(
                static::MESSAGE_CODE_TAG_WRONG_PACKAGE,
                $this->stackPos,
                static::CODE_TAG_WRONG_PACKAGE,
                [
                    $currentNamespace
                ]
            );

            if ($isFixing) {
                $this->fixWrongPackage($currentNamespace);
            }
        }
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'package';
    }
}
