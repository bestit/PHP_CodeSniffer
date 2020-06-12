<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\DocPosProviderTrait;
use function ucfirst;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;

/**
 * The basic sniff for the summaries.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
abstract class AbstractDocSniff extends AbstractSniff
{
    use DocPosProviderTrait;

    /**
     * Every doc comment block SHOULD start ucfirst.
     */
    public const CODE_DOC_COMMENT_UC_FIRST = 'DocCommentUcFirst';

    /**
     * Every doc comment block (the summary or a long description paragrah) SHOULD finish with double newline.
     */
    public const CODE_NO_LINE_AFTER_DOC_COMMENT = 'NoLineAfterDocComment';

    /**
     * There SHOULD be a summary.
     */
    public const CODE_NO_SUMMARY = 'NoSummary';

    /**
     * The summary SHOULD be in one line.
     */
    public const CODE_SUMMARY_TOO_LONG = 'SummaryTooLong';

    /**
     * Message that the doc comments does not start with an capital letter.
     */
    private const MESSAGE_DOC_COMMENT_UC_FIRST = 'The first letter of the summary/long-description is not uppercase.';

    /**
     * Message that there is no line after the doc comment.
     */
    private const MESSAGE_NO_LINE_AFTER_DOC_COMMENT = 'There is no empty line after the summary/long-description.';

    /**
     * Message that there is no summary in doc comment.
     */
    private const MESSAGE_NO_SUMMARY = 'There must be a summary in the doc comment.';

    /**
     * The error message if the summary is too long.
     */
    private const MESSAGE_SUMMARY_TOO_LONG = 'The summary should fit in one line. If you want more, use the long desc.';

    /**
     * The cached position of the summary.
     *
     * @var int|bool
     */
    private $summaryPosition = false;

    /**
     * Returns true if there is a doc block.
     *
     * @return bool
     */
    protected function areRequirementsMet(): bool
    {
        $docHelper = $this->getDocHelper();

        return $docHelper->hasDocBlock() && $docHelper->isMultiLine();
    }

    /**
     * Fixes the first letter of the doc comment, which must be uppercase.
     *
     * @param int $position
     * @param array $token
     *
     * @return void
     */
    private function fixDocCommentUcFirst(int $position, array $token): void
    {
        $this->file->fixer->beginChangeset();
        $this->file->fixer->replaceToken($position, ucfirst($token['content']));
        $this->file->fixer->endChangeset();
    }

    /**
     * Fixes no line after doc comment.
     *
     * @param int $position
     * @param array $token
     *
     * @return void
     */
    private function fixNoLineAfterDocComment(int $position, array $token): void
    {
        $this->file->fixer->beginChangeset();

        $this->file->fixer->addContent(
            $position,
            $this->file->eolChar . str_repeat('    ', $token['level']) . ' *'
        );

        $this->file->fixer->endChangeset();
    }

    /**
     * Returns the position of the summary or null.
     *
     * @return int|null
     */
    private function getSummaryPosition(): ?int
    {
        if ($this->summaryPosition === false) {
            $this->summaryPosition = $this->loadSummaryPosition();
        }

        return $this->summaryPosition;
    }

    /**
     * Returns true if the next line of the comment is empty.
     *
     * @param int $startPosition The position where to start the search.
     *
     * @return bool
     */
    private function isNextLineEmpty(int $startPosition): bool
    {
        $istNextLineEmpty = true;
        $nextRelevantPos = $this->loadNextDocBlockContent($startPosition);

        if ($nextRelevantPos !== false) {
            $istNextLineEmpty = $this->tokens[$startPosition]['line'] + 1 < $this->tokens[$nextRelevantPos]['line'];
        }

        return $istNextLineEmpty;
    }

    /**
     * Returns true if the prev line of the comment is empty.
     *
     * @param int $startPosition The position where to start the search.
     *
     * @return bool
     */
    private function isPrevLineEmpty(int $startPosition): bool
    {
        $isPrevLineEmpty = true;
        $posPrevContentPos = $this->loadPrevDocBlockContent($startPosition);

        if ($posPrevContentPos !== false) {
            $isPrevLineEmpty = $this->tokens[$startPosition]['line'] - 1 > $this->tokens[$posPrevContentPos]['line'];
        }

        return $isPrevLineEmpty;
    }

    /**
     * Is the given token a simple comment node?
     *
     * @param array $possCommentToken
     *
     * @return bool
     */
    private function isSimpleText(array $possCommentToken): bool
    {
        return $possCommentToken['code'] === T_DOC_COMMENT_STRING;
    }

    /**
     * Returns the position of the next whitespace or star of the comment for checking the line after that.
     *
     * @param int $startPosition
     *
     * @return int|bool
     */
    private function loadNextDocBlockContent(int $startPosition)
    {
        return $this->file->findNext(
            [
                T_DOC_COMMENT_WHITESPACE,
                T_DOC_COMMENT_STAR
            ],
            $startPosition + 1,
            $this->getDocHelper()->getBlockEndPosition(),
            true
        );
    }

    /**
     * Returns the position of the previous whitespace or star of the comment for checking the line after that.
     *
     * @param int $startPosition
     *
     * @return int|bool
     */
    private function loadPrevDocBlockContent(int $startPosition)
    {
        return $this->file->findPrevious(
            [
                T_DOC_COMMENT_OPEN_TAG,
                T_DOC_COMMENT_STAR,
                T_DOC_COMMENT_WHITESPACE,
            ],
            $startPosition - 1,
            $this->getDocCommentPos(),
            true
        );
    }

    /**
     * Loads the position of the summary token if possible.
     *
     * @return int|null
     */
    private function loadSummaryPosition(): ?int
    {
        $return = null;
        $possSummaryPos = $this->loadNextDocBlockContent($this->getDocCommentPos());

        if ((int) $possSummaryPos > 0) {
            $possSummaryToken = $this->tokens[$possSummaryPos];

            $return = $this->isSimpleText($possSummaryToken) ? $possSummaryPos : null;
        }

        return $return;
    }

    /**
     * Checks and registers errors  if there are invalid doc comments.
     *
     * @throws CodeWarning
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this
            ->validateSummaryExistence()
            ->validateDescriptions();
    }

    /**
     * Resets the sniff after one processing.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->resetDocCommentPos();
        $this->summaryPosition = false;
    }

    /**
     * Validates the descriptions in the file.
     *
     * @return AbstractDocSniff
     */
    private function validateDescriptions(): self
    {
        $commentPoss = TokenHelper::findNextAll(
            $this->file,
            [T_DOC_COMMENT_STRING, T_DOC_COMMENT_TAG],
            $this->getDocCommentPos(),
            $this->getDocHelper()->getBlockEndPosition()
        );

        foreach ($commentPoss as $index => $commentPos) {
            $commentToken = $this->tokens[$commentPos];
            $skipNewLineCheck = false;

            // We only search till the tags.
            if ($commentToken['code'] === T_DOC_COMMENT_TAG) {
                break;
            }

            if ($isFirstDocString = $index === 0) {
                $skipNewLineCheck = !$this->validateOneLineSummary();
            }

            $this->validateUCFirstDocComment($commentPos, $commentToken);

            if (!$skipNewLineCheck) {
                $this->validateNewLineAfterDocComment($commentPos, $commentToken, $isFirstDocString);
            }
        }

        return $this;

        ;
    }

    /**
     * Checks if there is a line break after the comment block..
     *
     * @param int $position
     * @param array $token
     * @param bool $asSingleLine
     *
     * @return void
     */
    private function validateNewLineAfterDocComment(int $position, array $token, bool $asSingleLine = true): void
    {
        if (!$this->isNextLineEmpty($position)) {
            $nextRelevantPos = $this->loadNextDocBlockContent($position);
            $nextToken = $this->tokens[$nextRelevantPos];

            // Register an error if we force a single line or this is no long description with more then one line.
            if ($asSingleLine || ($nextToken['code'] !== T_DOC_COMMENT_STRING)) {
                $isFixing = $this->file->addFixableWarning(
                    self::MESSAGE_NO_LINE_AFTER_DOC_COMMENT,
                    $position,
                    static::CODE_NO_LINE_AFTER_DOC_COMMENT
                );

                if ($isFixing) {
                    $this->fixNoLineAfterDocComment($position, $token);
                }
            }
        }
    }

    /**
     * Checks if the summary is on line or registers a warning.
     *
     * @return bool We can skip the new line error, so return true if the one line summary is true.
     */
    private function validateOneLineSummary(): bool
    {
        $isValid = true;
        $summaryPos = $this->getSummaryPosition();
        $nextPossiblePos = $this->loadNextDocBlockContent($summaryPos);

        if ($nextPossiblePos !== false && $nextPossiblePos > 0) {
            $nextToken = $this->tokens[$nextPossiblePos];

            if (($nextToken['code'] === T_DOC_COMMENT_STRING) && !$this->isNextLineEmpty($summaryPos)) {
                $isValid = false;

                $this->file->addWarning(
                    self::MESSAGE_SUMMARY_TOO_LONG,
                    $nextPossiblePos,
                    static::CODE_SUMMARY_TOO_LONG
                );
            }
        }

        return $isValid;
    }

    /**
     * Returns position to the comment summary or null.
     *
     * @throws CodeWarning If there is no summary.
     *
     * @return $this
     */
    private function validateSummaryExistence(): self
    {
        $summaryPos = $this->getSummaryPosition();

        if (!$summaryPos) {
            throw new CodeWarning(
                static::CODE_NO_SUMMARY,
                self::MESSAGE_NO_SUMMARY,
                $this->getDocCommentPos()
            );
        }

        return $this;
    }

    /**
     * Checks if the first char of the doc comment is ucfirst.
     *
     * @param int $position
     * @param array $token
     *
     * @return void
     */
    private function validateUCFirstDocComment(int $position, array $token): void
    {
        $commentText = $token['content'];

        if (ucfirst($commentText) !== $commentText && $this->isPrevLineEmpty($position)) {
            $isFixing = $this->file->addFixableWarning(
                self::MESSAGE_DOC_COMMENT_UC_FIRST,
                $position,
                static::CODE_DOC_COMMENT_UC_FIRST
            );

            if ($isFixing) {
                $this->fixDocCommentUcFirst($position, $token);
            }
        }
    }
}
