<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;
use BestIt\Sniffs\Commenting\AbstractDocSniff;

/**
 * Class DocTagSortingHelper
 *
 * @package BestIt\CodeSniffer\Helper
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class DocTagSortingHelper
{
    /**
     * The deferred CodeSniffer file.
     *
     * @var File
     */
    private $file;

    /**
     * The DocHelper.
     *
     * @var DocTagHelper
     */
    private $docTagHelper;

    /**
     * Array of tag metadata.
     *
     * @var array
     */
    private $tagMetadata;

    /**
     * DocTagSortingHelper constructor.
     *
     * @param File $file The deferred CodeSniffer file
     * @param DocTagHelper $docTagHelper The DocTagHelper
     */
    public function __construct(File $file, DocTagHelper $docTagHelper)
    {
        $this->file = $file;
        $this->docTagHelper = $docTagHelper;
    }

    /**
     * Checks the sorting of comment tags.
     *
     * @param array $tagMetadata Array of tag metadata
     *
     * @return bool Indicator if the sorting of the tags is correct
     */
    public function checkTagSorting(array $tagMetadata): bool
    {
        $this->tagMetadata = $tagMetadata;
        $unsortedTags = $this->docTagHelper->getCommentTagTokens();
        $sortedTagNames = $this->getExpandedSortedTags();

        $errorCount = 0;
        $currentTagIndex = 0;
        foreach ($unsortedTags as $unsortedTagToken) {
            $tagName = $unsortedTagToken['content'];
            $sortedTagName = $sortedTagNames[$currentTagIndex];
            $currentTagIndex++;

            if ($tagName === $sortedTagName) {
                continue;
            }

            $errorCount++;
            $this->file->addErrorOnLine(
                AbstractDocSniff::MESSAGE_TAG_WRONG_POSITION,
                $unsortedTagToken['line'],
                AbstractDocSniff::CODE_TAG_WRONG_POSITION,
                [
                    $sortedTagName
                ]
            );
        }

        return $errorCount === 0;
    }

    /**
     * Processes found tags - sorts them as needed and multiplies them how often they occur.
     *
     * @return array List of sorted and expanded tags
     */
    private function getExpandedSortedTags(): array
    {
        $tagNames = $this->getSortedTags();

        return $this->getExpandedCommentTags($tagNames);
    }

    /**
     * Returns sorted comment tag names.
     *
     * @return string[] List of current sorted comment tags
     */
    private function getSortedTags(): array
    {
        $commentTags = $this->docTagHelper->getCommentTagTokens();
        $sortedMetaTags = array_keys($this->tagMetadata);

        $tagNames = array_unique(
            array_column($commentTags, 'content')
        );

        uasort($tagNames, function ($tagA, $tagB) use ($sortedMetaTags) {
            $expectedPosA = array_search($tagA, $sortedMetaTags, true);
            $expectedPosB = array_search($tagB, $sortedMetaTags, true);

            if ($expectedPosA === false) {
                $expectedPosA = PHP_INT_MAX;
            }

            if ($expectedPosB === false) {
                $expectedPosB = PHP_INT_MAX;
            }

            if ($expectedPosA === $expectedPosB) {
                return 0;
            }

            return ($expectedPosA < $expectedPosB) ? -1 : 1;
        });

        return array_values($tagNames);
    }

    /**
     * Returns tagNames expanded by their count.
     *
     * @param string[] $tagNames List of tag names
     *
     * @return string[] Expanded list of tag names
     */
    private function getExpandedCommentTags(array $tagNames): array
    {
        $commentTags = $this->docTagHelper->getCommentTagTokens();
        $tagCounts = $this->docTagHelper->getTagCounts($commentTags);
        $expandedTags = [];

        foreach ($tagNames as $tagName) {
            $tagCount = $tagCounts[$tagName];

            $expandedTags = array_merge(
                $expandedTags,
                array_fill(0, $tagCount, $tagName)
            );
        }

        return $expandedTags;
    }
}
