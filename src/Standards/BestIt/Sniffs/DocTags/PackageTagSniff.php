<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\Helper\TokenHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use const T_DOC_COMMENT_STRING;
use const T_NAMESPACE;

/**
 * Checks if the package contains the actual namespace.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class PackageTagSniff extends AbstractTagSniff
{
    /**
     * If there is a namespace, you MUST provide this namespace as package tag.
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
        $this->file->fixer->replaceToken(
            TokenHelper::findNext($this->file, [T_DOC_COMMENT_STRING], $this->stackPos),
            $currentNamespace,
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

        if (
            ((int) $this->file->findPrevious([T_NAMESPACE], $this->stackPos) > 0) && $currentNamespace &&
            $tagContent !== $currentNamespace
        ) {
            $isFixing = $this->file->addFixableError(
                static::MESSAGE_CODE_TAG_WRONG_PACKAGE,
                $this->stackPos,
                static::CODE_TAG_WRONG_PACKAGE,
                [
                    $currentNamespace,
                ],
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
