<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\CodeSniffer\Helper\DocDescriptionHelper;
use BestIt\CodeSniffer\Helper\DocHelper;
use BestIt\CodeSniffer\Helper\DocSummaryHelper;
use BestIt\CodeSniffer\Helper\PropertyHelper;
use BestIt\Sniffs\AbstractSniff;
use const T_VARIABLE;

/**
 * Class AbstractDocSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
abstract class AbstractDocSniff extends AbstractSniff
{
    /**
     * Defines the maximum line length.
     *
     * @var int
     */
    public const MAX_LINE_LENGTH = 120;

    /**
     * Code that there is no immediate doc comment found before class.
     *
     * @var string
     */
    public const CODE_NO_IMMEDIATE_DOC_FOUND = 'NoImmediateDocFound';

    /**
     * Message that there is no immediate doc comment found before class.
     *
     * @var string
     */
    public const MESSAGE_NO_IMMEDIATE_DOC_FOUND = 'No immediate doc comment found before class.';

    /**
     * Code that there is no summary in doc comment.
     *
     * @var string
     */
    public const CODE_NO_SUMMARY = 'NoSummary';

    /**
     * Message that there is no summary in doc comment.
     *
     * @var string
     */
    public const MESSAGE_NO_SUMMARY = 'There must be a summary for the doc comment.';

    /**
     * Code that there is no summary in doc comment.
     *
     * @var string
     */
    public const CODE_SUMMARY_NOT_FIRST = 'SummaryNotFirst';

    /**
     * Message that there is no summary in doc comment.
     *
     * @var string
     */
    public const MESSAGE_SUMMARY_NOT_FIRST = 'The summary must be the first statement in a comment.';

    /**
     * Code that the summary is too long.
     *
     * @var string
     */
    public const CODE_SUMMARY_TOO_LONG = 'SummaryTooLong';

    /**
     * Message that there is no summary in doc comment.
     *
     * @var string
     */
    public const MESSAGE_SUMMARY_TOO_LONG = 'The summary line must not be longer than 120 chars.';

    /**
     * Code that the summary is not multi line.
     *
     * @var string
     */
    public const CODE_COMMENT_NOT_MULTI_LINE = 'CommentNotMultiLine';

    /**
     * Message that comment is not multi line.
     *
     * @var string
     */
    public const MESSAGE_COMMENT_NOT_MULTI_LINE = 'Comment is not multi line.';

    /**
     * Code that there is no line after summary.
     *
     * @var string
     */
    public const CODE_NO_LINE_AFTER_SUMMARY = 'NoLineAfterSummary';

    /**
     * Message that there is no line after summary.
     *
     * @var string
     */
    public const MESSAGE_NO_LINE_AFTER_SUMMARY = 'There is no empty line after the summary.';

    /**
     * Code that the line after the summary is not empty.
     *
     * @var string
     */
    public const CODE_LINE_AFTER_SUMMARY_NOT_EMPTY = 'LineAfterSummaryNotEmpty';

    /**
     * Message that the line after the summary is not empty.
     *
     * @var string
     */
    public const MESSAGE_LINE_AFTER_SUMMARY_NOT_EMPTY = 'The line after the summary is not empty.';

    /**
     * Code that no comment description is found.
     *
     * @var string
     */
    public const CODE_DESCRIPTION_NOT_FOUND = 'DescriptionNotFound';

    /**
     * Message that no comment description is found.
     *
     * @var string
     */
    public const MESSAGE_DESCRIPTION_NOT_FOUND = 'There must be an comment description.';

    /**
     * Code that there is no empty line after description.
     *
     * @var string
     */
    public const CODE_NO_LINE_AFTER_DESCRIPTION = 'NoLineAfterDescription';

    /**
     * Message that there is no empty line after description.
     *
     * @var string
     */
    public const MESSAGE_NO_LINE_AFTER_DESCRIPTION = 'There must be an empty line after description.';

    /**
     * Code that there is no empty line after description.
     *
     * @var string
     */
    public const CODE_MUCH_LINES_AFTER_DESCRIPTION = 'MuchLinesAfterDescription';

    /**
     * Message that there is no empty line after description.
     *
     * @var string
     */
    public const MESSAGE_MUCH_LINES_AFTER_DESCRIPTION = 'There must be exactly one empty line after description.';

    /**
     * Code that the description line is too long.
     *
     * @var string
     */
    public const CODE_DESCRIPTION_TOO_LONG = 'DescriptionTooLong';

    /**
     * Message that the description line is too long.
     *
     * @var string
     */
    public const MESSAGE_DESCRIPTION_TOO_LONG = 'The description exceeds the maximum length of 120 chars.';

    /**
     * Code that the summary starts with an capital letter.
     *
     * @var string
     */
    public const CODE_SUMMARY_UC_FIRST = 'SummaryUcFirst';

    /**
     * Message that the summary starts with an capital letter.
     *
     * @var string
     */
    public const MESSAGE_SUMMARY_UC_FIRST = 'The first letter of the summary is not uppercase';

    /**
     * Code that the description starts with an capital letter.
     *
     * @var string
     */
    public const CODE_DESCRIPTION_UC_FIRST = 'DescriptionUcFirst';

    /**
     * Message that the description starts with an capital letter.
     *
     * @var string
     */
    public const MESSAGE_DESCRIPTION_UC_FIRST = 'The first letter of the description is not uppercase';

    /**
     * Code that the tag content has a mixed type warning.
     *
     * @var string
     */
    public const CODE_TAG_WARNING_MIXED = 'TagWarningMixedType';

    /**
     * Message that the tag content has a mixed type warning.
     *
     * @var string
     */
    public const MESSAGE_TAG_WARNING_MIXED = 'Consider removing the mixed type';

    /**
     * Indicator if a description is required.
     *
     * @var bool
     */
    public $descriptionRequired = false;

    /**
     * Processes a found registered token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $isVariable = false;

        $propertyHelper = new PropertyHelper($this->file);

        if ($this->token['code'] === T_VARIABLE
            && !$propertyHelper->isProperty($this->stackPos)
        ) {
            $isVariable = true;
        }

        $docHelper = new DocHelper($this->file, $this->stackPos);
        $summaryHelper = new DocSummaryHelper($this->file, $docHelper);
        $descriptionHelper = new DocDescriptionHelper(
            $this->file,
            $docHelper,
            $summaryHelper
        );

        if (!$docHelper->checkCommentExists($this->stackPos, $isVariable)
            || !$docHelper->checkCommentMultiLine($isVariable)
        ) {
            return;
        }

        if (!$isVariable) {
            $summaryHelper->checkCommentSummary();

            $descriptionHelper->checkCommentDescription(
                $this->descriptionRequired
            );
        }
    }
}
