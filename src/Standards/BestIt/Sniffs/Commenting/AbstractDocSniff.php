<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\CodeSniffer\Helper\DocDescriptionHelper;
use BestIt\CodeSniffer\Helper\DocHelper;
use BestIt\CodeSniffer\Helper\DocSummaryHelper;
use BestIt\CodeSniffer\Helper\DocTagHelper;
use BestIt\CodeSniffer\Helper\PropertyHelper;
use BestIt\CodeSniffer\AbstractSniff;

/**
 * Class AbstractDocSniff
 *
 * @package BestIt\Sniffs\Commenting
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
abstract class AbstractDocSniff extends AbstractSniff
{
    /**
     * Defines the maximum line length.
     *
     * @var int
     */
    const MAX_LINE_LENGTH = 120;

    /**
     * Code that there is no immediate doc comment found before class.
     *
     * @var string
     */
    const CODE_NO_IMMEDIATE_DOC_FOUND = 'NoImmediateDocFound';

    /**
     * Message that there is no immediate doc comment found before class.
     *
     * @var string
     */
    const MESSAGE_NO_IMMEDIATE_DOC_FOUND = 'No immediate doc comment found before class.';

    /**
     * Code that there is no summary in doc comment.
     *
     * @var string
     */
    const CODE_NO_SUMMARY = 'NoSummary';

    /**
     * Message that there is no summary in doc comment.
     *
     * @var string
     */
    const MESSAGE_NO_SUMMARY = 'There must be a summary for the doc comment.';

    /**
     * Code that there is no summary in doc comment.
     *
     * @var string
     */
    const CODE_SUMMARY_NOT_FIRST = 'SummaryNotFirst';

    /**
     * Message that there is no summary in doc comment.
     *
     * @var string
     */
    const MESSAGE_SUMMARY_NOT_FIRST = 'The summary must be the first statement in a comment.';

    /**
     * Code that the summary is too long.
     *
     * @var string
     */
    const CODE_SUMMARY_TOO_LONG = 'SummaryTooLong';

    /**
     * Message that there is no summary in doc comment.
     *
     * @var string
     */
    const MESSAGE_SUMMARY_TOO_LONG = 'The summary line must not be longer than 120 chars.';

    /**
     * Code that the summary is not multi line.
     *
     * @var string
     */
    const CODE_COMMENT_NOT_MULTI_LINE = 'CommentNotMultiLine';

    /**
     * Message that comment is not multi line.
     *
     * @var string
     */
    const MESSAGE_COMMENT_NOT_MULTI_LINE = 'Comment is not multi line.';

    /**
     * Code that there is no line after summary.
     *
     * @var string
     */
    const CODE_NO_LINE_AFTER_SUMMARY = 'NoLineAfterSummary';

    /**
     * Message that there is no line after summary.
     *
     * @var string
     */
    const MESSAGE_NO_LINE_AFTER_SUMMARY = 'There is no empty line after the summary.';

    /**
     * Code that the line after the summary is not empty.
     *
     * @var string
     */
    const CODE_LINE_AFTER_SUMMARY_NOT_EMPTY = 'LineAfterSummaryNotEmpty';

    /**
     * Message that the line after the summary is not empty.
     *
     * @var string
     */
    const MESSAGE_LINE_AFTER_SUMMARY_NOT_EMPTY = 'The line after the summary is not empty.';

    /**
     * Code that no comment description is found.
     *
     * @var string
     */
    const CODE_DESCRIPTION_NOT_FOUND = 'DescriptionNotFound';

    /**
     * Message that no comment description is found.
     *
     * @var string
     */
    const MESSAGE_DESCRIPTION_NOT_FOUND = 'There must be an comment description.';

    /**
     * Code that there is no empty line after description.
     *
     * @var string
     */
    const CODE_NO_LINE_AFTER_DESCRIPTION = 'NoLineAfterDescription';

    /**
     * Message that there is no empty line after description.
     *
     * @var string
     */
    const MESSAGE_NO_LINE_AFTER_DESCRIPTION = 'There must be an empty line after description.';

    /**
     * Code that there is no empty line after description.
     *
     * @var string
     */
    const CODE_MUCH_LINES_AFTER_DESCRIPTION = 'MuchLinesAfterDescription';

    /**
     * Message that there is no empty line after description.
     *
     * @var string
     */
    const MESSAGE_MUCH_LINES_AFTER_DESCRIPTION = 'There must be exactly one empty line after description.';

    /**
     * Code that the description line is too long.
     *
     * @var string
     */
    const CODE_DESCRIPTION_TOO_LONG = 'DescriptionTooLong';

    /**
     * Message that the description line is too long.
     *
     * @var string
     */
    const MESSAGE_DESCRIPTION_TOO_LONG = 'The description exceeds the maximum length of 120 chars.';

    /**
     * Code that comment tag is not allowed.
     *
     * @var string
     */
    const CODE_TAG_NOT_ALLOWED = 'TagNotAllowed';

    /**
     * Message that comment tag is not allowed.
     *
     * @var string
     */
    const MESSAGE_TAG_NOT_ALLOWED = 'The comment tag "%s" is not allowed.';

    /**
     * Code that comment tag must appear minimum x times.
     *
     * @var string
     */
    const CODE_TAG_OCCURRENCE_MIN = 'TagOccurrenceMin';

    /**
     * Message that comment tag must appear minimum x times.
     *
     * @var string
     */
    const MESSAGE_TAG_OCCURRENCE_MIN = 'The comment tag "%s" must appear minimum %d times.';

    /**
     * Code that comment tag must appear maximum x times.
     *
     * @var string
     */
    const CODE_TAG_OCCURRENCE_MAX = 'TagOccurrenceMax';

    /**
     * Message that comment tag must appear maximum x times.
     *
     * @var string
     */
    const MESSAGE_TAG_OCCURRENCE_MAX = 'The comment tag "%s" must appear maximum %d times.';

    /**
     * Code that comment tag has the wrong position.
     *
     * @var string
     */
    const CODE_TAG_WRONG_POSITION = 'TagWrongPosition';

    /**
     * Message that comment tag has the wrong position.
     *
     * @var string
     */
    const MESSAGE_TAG_WRONG_POSITION = 'This tag has the wrong position. Expected "%s"';

    /**
     * Code that the summary starts with an capital letter.
     *
     * @var string
     */
    const CODE_SUMMARY_UC_FIRST = 'SummaryUcFirst';

    /**
     * Message that the summary starts with an capital letter.
     *
     * @var string
     */
    const MESSAGE_SUMMARY_UC_FIRST = 'The first letter of the summary is not uppercase';

    /**
     * Code that the description starts with an capital letter.
     *
     * @var string
     */
    const CODE_DESCRIPTION_UC_FIRST = 'DescriptionUcFirst';

    /**
     * Message that the description starts with an capital letter.
     *
     * @var string
     */
    const MESSAGE_DESCRIPTION_UC_FIRST = 'The first letter of the description is not uppercase';

    /**
     * Code that there is no line after given tag.
     *
     * @var string
     */
    const CODE_NO_LINE_AFTER_TAG = 'NoLineAfterTag';

    /**
     * Message that there is no line after given tag.
     *
     * @var string
     */
    const MESSAGE_NO_LINE_AFTER_TAG = 'There is no blank line after tag %s';

    /**
     * Code that there are too much lines after given tag.
     *
     * @var string
     */
    const CODE_MUCH_LINES_AFTER_TAG = 'MuchLinesAfterTag';

    /**
     * Message that there are too much lines after given tag.
     *
     * @var string
     */
    const MESSAGE_MUCH_LINES_AFTER_TAG = 'There are too much blank lines after tag %s';

    /**
     * Code that the tag content format is invalid.
     *
     * @var string
     */
    const CODE_TAG_CONTENT_FORMAT_INVALID = 'TagFormatContentInvalid';

    /**
     * Message that the tag content format is invalid.
     *
     * @var string
     */
    const MESSAGE_TAG_CONTENT_FORMAT_INVALID = '"%s"-Tag format is invalid. Expected: "%s"';

    /**
     * Message that the tag content format is invalid.
     *
     * @var string
     */
    const MESSAGE_TAG_CONTENT_FORMAT_WARNING = '"%s"-Tag format may be invalid. Expected: "%s"';

    /**
     * Code that the tag content format may be invalid.
     *
     * @var string
     */
    const CODE_TAG_CONTENT_FORMAT_WARNING = 'TagFormatContentWarning';

    /**
     * DocHelper for comment
     *
     * @var DocHelper
     */
    private $docHelper;

    /**
     * Helper for comment summary.
     *
     * @var DocSummaryHelper
     */
    protected $summaryHelper;

    /**
     * The doc comment description helper.
     *
     * @var DocDescriptionHelper
     */
    private $descriptionHelper;

    /**
     * The doc comment helper
     *
     * @var DocTagHelper
     */
    private $tagHelper;

    /**
     * Indicator if a description is required.
     *
     * @var bool
     */
    public $descriptionRequired = false;

    /**
     * Returns an array of registered tokens.
     *
     * @return int[] Returns array of tokens to listen for
     */
    public function getRegisteredTokens(): array
    {
        return $this->getListenedTokens();
    }

    /**
     * Processes a found registered token.
     *
     * @return void
     */
    public function processToken()
    {
        $propertyHelper = new PropertyHelper($this->getFile());

        if ($this->getListenerToken()['code'] === T_VARIABLE
            && !$propertyHelper->isProperty($this->getListenerPointer())
        ) {
            return;
        }

        $this->docHelper = new DocHelper($this->getFile(), $this->getListenerPointer());
        $this->summaryHelper = new DocSummaryHelper($this->getFile(), $this->docHelper);
        $this->descriptionHelper = new DocDescriptionHelper(
            $this->getFile(),
            $this->docHelper,
            $this->summaryHelper
        );
        $this->tagHelper = new DocTagHelper($this->getFile(), $this->docHelper, $this->getListenerPointer());

        if (!$this->docHelper->checkCommentExists($this->getListenerPointer())
            || !$this->docHelper->checkCommentMultiLine()
        ) {
            return;
        }

        $this->summaryHelper->checkCommentSummary();

        $this->descriptionHelper->checkCommentDescription(
            $this->descriptionRequired
        );

        $this->tagHelper->checkCommentTags(
            $this->getTagMetadata(),
            $this->getDisallowedTags()
        );
    }

    /**
     * Returns TagHelper
     *
     * @return DocTagHelper Returns doc comment tag helper for callable function
     */
    public function getTagHelper(): DocTagHelper
    {
        return $this->tagHelper;
    }

    /**
     * Returns which tokens should be listened to.
     *
     * @return int[] List of tokens to listen for
     */
    abstract public function getListenedTokens(): array;

    /**
     * Returns allowed tag occurrences.
     *
     * @return array List of tag metadata
     */
    abstract public function getTagMetadata(): array;

    /**
     * Returns an array of disallowed tags.
     *
     * @return array List of disallowed tags
     */
    abstract public function getDisallowedTags(): array;
}
