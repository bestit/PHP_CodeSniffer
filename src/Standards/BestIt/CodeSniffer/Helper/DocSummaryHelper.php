<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;
use BestIt\Sniffs\Commenting\AbstractDocSniff;

/**
 * Class DocSummaryHelper
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class DocSummaryHelper
{
    /**
     * The php cs file.
     *
     * @var File
     */
    private $file;

    /**
     * The doc comment helper.
     *
     * @var DocHelper
     */
    private $docHelper;

    /**
     * Token stack of the current file.
     *
     * @var array
     */
    private $tokens;

    /**
     * DocSummaryHelper constructor.
     *
     * @param File $file The php cs file
     * @param DocHelper $docHelper The doc comment helper
     */
    public function __construct(File $file, DocHelper $docHelper)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();
        $this->docHelper = $docHelper;
    }

    /**
     * Returns pointer to the comment summary.
     *
     * @return int Pointer to the token or -1
     */
    public function getCommentSummaryPointer(): int
    {
        $commentStartPtr = $this->docHelper->getCommentStartPointer();
        $commentEndPtr = $this->docHelper->getCommentEndPointer();

        $summaryPtr = $this->file->findNext(
            [
                T_DOC_COMMENT_WHITESPACE,
                T_DOC_COMMENT_STAR
            ],
            $commentStartPtr + 1,
            $commentEndPtr,
            true
        );

        $summaryToken = $this->tokens[$summaryPtr];

        return $summaryToken['code'] === T_DOC_COMMENT_STRING ? $summaryPtr : -1;
    }

    /**
     * Returns comment summary token.
     *
     * @return array Summary token array
     */
    public function getCommentSummaryToken(): array
    {
        return $this->tokens[$this->getCommentSummaryPointer()];
    }

    /**
     * Check class comment summary.
     *
     * @return void
     */
    public function checkCommentSummary()
    {
        $commentEndPtr = $this->docHelper->getCommentEndPointer();
        $commentStartPtr = $this->docHelper->getCommentStartPointer();
        $commentStartToken = $this->docHelper->getCommentStartToken();

        $summaryPtr = $this->getCommentSummaryPointer();

        if ($summaryPtr === -1) {
            $this->file->addError(
                AbstractDocSniff::MESSAGE_NO_SUMMARY,
                $commentStartPtr,
                AbstractDocSniff::CODE_NO_SUMMARY
            );

            return;
        }

        $summaryToken = $this->tokens[$summaryPtr];

        $this->checkSummaryIsFirstLine($commentStartToken, $summaryToken, $summaryPtr);
        $this->checkSummaryCapitalLetter($summaryToken, $summaryPtr);
        $this->checkSummaryLineLength($summaryToken, $summaryPtr);
        $this->checkLineAfterSummary($summaryPtr, $commentEndPtr);
    }

    /**
     * Checks that the first character of the summary is upper case.
     *
     * @param array $summaryToken Token of the summary
     * @param int $summaryPtr Pointer to the summary
     *
     * @return void
     */
    private function checkSummaryCapitalLetter(array $summaryToken, int $summaryPtr)
    {
        $summaryText = $summaryToken['content'];

        if (ucfirst($summaryText) === $summaryText) {
            return;
        }

        $fixUcFirst = $this->file->addFixableError(
            AbstractDocSniff::MESSAGE_SUMMARY_UC_FIRST,
            $summaryPtr,
            AbstractDocSniff::CODE_SUMMARY_UC_FIRST
        );

        if ($fixUcFirst) {
            $this->fixSummaryUcFirst($summaryToken, $summaryPtr);
        }
    }

    /**
     * Checks if the line length of the summary is maximum 120 chars.
     *
     * @param array $summaryToken Token array of the summary
     * @param int $summaryPtr Pointer of the summary
     *
     * @return void
     */
    private function checkSummaryLineLength(array $summaryToken, int $summaryPtr)
    {
        $summaryLineLength = $summaryToken['column'] + $summaryToken['length'];

        if ($summaryLineLength > AbstractDocSniff::MAX_LINE_LENGTH) {
            $this->file->addError(
                AbstractDocSniff::MESSAGE_SUMMARY_TOO_LONG,
                $summaryPtr,
                AbstractDocSniff::CODE_SUMMARY_TOO_LONG
            );
        }
    }

    /**
     * Checks if the summary is the first line of the comment.
     *
     * @param array $commentStartToken Token array of the comment start
     * @param array $summaryToken Token of the summary
     * @param int $summaryPtr Pointer to the summary
     *
     * @return void
     */
    private function checkSummaryIsFirstLine(array $commentStartToken, array $summaryToken, int $summaryPtr)
    {
        if ($summaryToken['line'] !== $commentStartToken['line'] + 1) {
            $fixSummaryNotFirst = $this->file->addFixableError(
                AbstractDocSniff::MESSAGE_SUMMARY_NOT_FIRST,
                $summaryPtr,
                AbstractDocSniff::CODE_SUMMARY_NOT_FIRST
            );

            if ($fixSummaryNotFirst) {
                $this->fixSummaryNotFirst();
            }
        }
    }

    /**
     * Checks the line after the summary.
     *
     * @param int $summaryPtr Pointer to the summary
     * @param int $commentEndPtr Pointer to the end of the doc comment
     *
     * @return void
     */
    private function checkLineAfterSummary(int $summaryPtr, int $commentEndPtr)
    {
        $summaryToken = $this->getCommentSummaryToken();

        $nextRelevantPtr = $this->file->findNext(
            [
                T_DOC_COMMENT_WHITESPACE,
                T_DOC_COMMENT_STAR
            ],
            $summaryPtr + 1,
            $commentEndPtr,
            true
        );

        if ($nextRelevantPtr === -1) {
            return;
        }

        $nextRelevantToken = $this->tokens[$nextRelevantPtr];

        if (($nextRelevantToken['line'] - $summaryToken['line']) === 1) {
            $fixLineAfterSummary = $this->file->addFixableError(
                AbstractDocSniff::MESSAGE_NO_LINE_AFTER_SUMMARY,
                $summaryPtr,
                AbstractDocSniff::CODE_NO_LINE_AFTER_SUMMARY
            );

            if ($fixLineAfterSummary) {
                $this->fixNoLineAfterSummary();
            }
        }
    }

    /**
     * Fixes no line after summary.
     *
     * @return void
     */
    private function fixNoLineAfterSummary()
    {
        $summaryPtr = $this->getCommentSummaryPointer();
        $summaryToken = $this->getCommentSummaryToken();

        $this->file->getFixer()->beginChangeset();

        $this->file->getFixer()->addContent(
            $summaryPtr,
            $this->file->getEolChar() . str_repeat('    ', $summaryToken['level']) . ' *'
        );

        $this->file->getFixer()->endChangeset();
    }

    /**
     * Fixes summary not first statement.
     *
     * @return void
     */
    private function fixSummaryNotFirst()
    {
        $commentStartToken = $this->docHelper->getCommentStartToken();
        $summaryStartToken = $this->getCommentSummaryToken();

        $startLine = $commentStartToken['line'] + 1;
        $endLine = $summaryStartToken['line'] - 1;

        $this->file->getFixer()->beginChangeset();

        (new LineHelper($this->file))->removeLines($startLine, $endLine);

        $this->file->getFixer()->endChangeset();
    }

    /**
     * Fixes the first letter of the summary to be uppercase.
     *
     * @param array $summaryToken Token array of the summary
     * @param int $summaryPtr Pointer to the summary
     *
     * @return void
     */
    private function fixSummaryUcFirst(array $summaryToken, int $summaryPtr)
    {
        $this->file->getFixer()->beginChangeset();

        $this->file->getFixer()->replaceToken($summaryPtr, ucfirst($summaryToken['content']));

        $this->file->getFixer()->endChangeset();
    }
}
