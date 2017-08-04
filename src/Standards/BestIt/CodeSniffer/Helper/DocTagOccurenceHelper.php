<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;
use BestIt\Sniffs\Commenting\AbstractDocSniff;

/**
 * Class DocTagOccurenceHelper
 *
 * @package BestIt\CodeSniffer\Helper
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class DocTagOccurenceHelper
{
    /**
     * The deferred CodeSniffer file.
     *
     * @var File
     */
    private $file;

    /**
     * The DocHelper
     *
     * @var DocHelper
     */
    private $docHelper;

    /**
     * The DocTagHelper
     *
     * @var DocTagHelper
     */
    private $docTagHelper;

    /**
     * DocTagOccurenceHelper constructor.
     *
     * @param File $file The deferred CodeSniffer file.
     * @param DocHelper $docHelper The DocHelper
     * @param DocTagHelper $docTagHelper The DocTagHelper
     */
    public function __construct(File $file, DocHelper $docHelper, DocTagHelper $docTagHelper)
    {
        $this->docTagHelper = $docTagHelper;
        $this->docHelper = $docHelper;
        $this->file = $file;
    }

    /**
     * Checks the occurrence of comment tags.
     *
     * @return void
     */
    public function checkTagOccurrences(array $tagMetadata): void
    {
        $tagTokens = $this->docTagHelper->getCommentTagTokens();

        $tagCounts = $this->docTagHelper->getTagCounts($tagTokens);

        foreach ($tagMetadata as $tagName => $occurrence) {
            $min = $occurrence['min'];
            $max = $occurrence['max'];

            if (isset($occurrence['if']) && !$occurrence['if']()) {
                continue;
            }

            $tagCount = 0;

            if (isset($tagCounts[$tagName])) {
                $tagCount = $tagCounts[$tagName];
            }

            if ($min !== 0 && $tagCount < $min) {
                $this->file->addError(
                    AbstractDocSniff::MESSAGE_TAG_OCCURRENCE_MIN,
                    $this->docHelper->getCommentStartPointer(),
                    AbstractDocSniff::CODE_TAG_OCCURRENCE_MIN,
                    [
                        $tagName,
                        $min
                    ]
                );

                continue;
            }

            if ($max !== null && $tagCount > $max) {
                $this->file->addError(
                    AbstractDocSniff::MESSAGE_TAG_OCCURRENCE_MAX,
                    $this->docHelper->getCommentStartPointer(),
                    AbstractDocSniff::CODE_TAG_OCCURRENCE_MAX,
                    [
                        $tagName,
                        $max
                    ]
                );
            }
        }
    }
}
