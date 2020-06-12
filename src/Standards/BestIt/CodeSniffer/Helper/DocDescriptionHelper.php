<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\Sniffs\Commenting\AbstractDocSniff;
use PHP_CodeSniffer\Files\File;

/**
 * Class DocDescriptionHelper
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class DocDescriptionHelper
{
    /**
     * The php code sniffer file.
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
     * The token stack of the cs file.
     *
     * @var array
     */
    private $tokens;

    /**
     * The doc comment summary helper.
     *
     * @var DocSummaryHelper
     */
    private $summaryHelper;

    /**
     * Indicator if a description is required.
     *
     * @var bool
     */
    private $descriptionRequired;

    /**
     * DocSummaryHelper constructor.
     *
     * @param File $file The php cs file
     * @param DocHelper $docHelper The doc comment helper
     * @param DocSummaryHelper $summaryHelper The doc comment summary helper
     */
    public function __construct(File $file, DocHelper $docHelper, DocSummaryHelper $summaryHelper)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();
        $this->docHelper = $docHelper;
        $this->summaryHelper = $summaryHelper;
    }

    /**
     * Checks for comment description.
     *
     * @param bool $descriptionRequired Indicator if the description is required.
     *
     * @return void
     */
    public function checkCommentDescription(bool $descriptionRequired): void
    {
        $this->descriptionRequired = $descriptionRequired;

        $commentStartToken = $this->docHelper->getBlockStartToken();
        $commentEndToken = $this->docHelper->getBlockEndToken();

        $summaryPtr = $this->summaryHelper->getCommentSummaryPosition();

        if ($summaryPtr === -1) {
            return;
        }

        $hasTags = count($commentStartToken['comment_tags']) > 0;

        $descriptionStartPtr = $this->getCommentDescriptionStartPointer();

        if ($descriptionStartPtr === -1) {
            $this->addDescriptionNotFoundError($summaryPtr);

            return;
        }

        $descriptionEndPtr = $this->getCommentDescriptionEndPointer();
        $descEndToken = $this->tokens[$descriptionEndPtr];

        $this->checkCommentDescriptionUcFirst($descriptionStartPtr);

        //Fix no or too much lines after description.
        $toLine = $commentEndToken['line'];
        $expectedLines = 0;

        if ($hasTags) {
            $firstTagPtr = array_shift($commentStartToken['comment_tags']);
            $firstTagToken = $this->tokens[$firstTagPtr];
            $toLine = $firstTagToken['line'];

            $expectedLines = 1;
        }

        $diffLines = $toLine - $descEndToken['line'] - 1;

        if ($diffLines === $expectedLines) {
            return;
        }

        if ($diffLines < $expectedLines && $hasTags) {
            $fixNoLine = $this->file->addFixableError(
                AbstractDocSniff::MESSAGE_NO_LINE_AFTER_DESCRIPTION,
                $descriptionEndPtr,
                AbstractDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION
            );

            if ($fixNoLine) {
                $this->fixNoLineAfterDescription();
            }

            return;
        }

        $fixMuchLines = $this->file->addFixableError(
            AbstractDocSniff::MESSAGE_MUCH_LINES_AFTER_DESCRIPTION,
            $descriptionEndPtr,
            AbstractDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION
        );

        if ($fixMuchLines) {
            $this->fixMuchLinesAfterDescription($descEndToken['line'] + 1, $toLine - 1);
        }
    }

    /**
     * Checks if the description starts with a capital letter.
     *
     * @param int $descriptionStartPtr Pointer to the start of the description.
     *
     * @return void
     */
    private function checkCommentDescriptionUcFirst(int $descriptionStartPtr): void
    {
        $descStartToken = $this->tokens[$descriptionStartPtr];

        $descriptionContent = $descStartToken['content'];

        if (ucfirst($descriptionContent) === $descriptionContent) {
            return;
        }

        $fixUcFirst = $this->file->addFixableError(
            AbstractDocSniff::MESSAGE_DESCRIPTION_UC_FIRST,
            $descriptionStartPtr,
            AbstractDocSniff::CODE_DESCRIPTION_UC_FIRST
        );

        if ($fixUcFirst) {
            $this->fixDescriptionUcFirst($descriptionStartPtr);
        }
    }

    /**
     * Returns pointer to the end of the description.
     *
     * @return int Pointer to the end of the description or false
     */
    private function getCommentDescriptionEndPointer(): int
    {
        $descriptionStartPtr = $this->getCommentDescriptionStartPointer();

        $commentStartToken = $this->docHelper->getBlockStartToken();
        $commentEndPtr = $this->docHelper->getBlockEndPosition();

        //If no tags found, possible end of search is the starting tag of the doc comment.
        if (count($commentStartToken['comment_tags']) === 0) {
            return $this->file->findPrevious(
                [T_DOC_COMMENT_STRING],
                $commentEndPtr - 1,
                $descriptionStartPtr
            );
        }

        //else its the pointer of the first comment tag found.
        $firstTagPtr = array_shift($commentStartToken['comment_tags']);

        return $this->file->findPrevious(
            [T_DOC_COMMENT_STRING],
            $firstTagPtr - 1,
            $descriptionStartPtr
        );
    }

    /**
     * Returns pointer to the start of the long description or false if not found.
     *
     * @return int Pointer to the start of the description or -1
     */
    private function getCommentDescriptionStartPointer(): int
    {
        $commentStartToken = $this->docHelper->getBlockStartToken();
        $commentEndPtr = $this->docHelper->getBlockEndPosition();

        $summaryPtr = $this->summaryHelper->getCommentSummaryPosition();

        //If no tags the possible end of search is the closing tag of the doc comment.
        if (count($commentStartToken['comment_tags']) === 0) {
            return $this->file->findNext(
                [T_DOC_COMMENT_STRING],
                $summaryPtr + 1,
                $commentEndPtr
            );
        }

        //else its the pointer of the first comment tag found.
        $firstTagPtr = array_shift($commentStartToken['comment_tags']);

        return $this->file->findNext(
            [T_DOC_COMMENT_STRING],
            $summaryPtr + 1,
            $firstTagPtr - 1
        );
    }

    /**
     * Adds error when description is not found.
     *
     * @param int $summaryPtr Pointer to summary token.
     *
     * @return void
     */
    private function addDescriptionNotFoundError(int $summaryPtr): void
    {
        if ($this->descriptionRequired) {
            $this->file->addError(
                AbstractDocSniff::MESSAGE_DESCRIPTION_NOT_FOUND,
                $summaryPtr,
                AbstractDocSniff::CODE_DESCRIPTION_NOT_FOUND
            );
        }
    }

    /**
     * Fixes no line after description.
     *
     * @return void
     */
    private function fixNoLineAfterDescription(): void
    {
        $descEndPtr = $this->getCommentDescriptionEndPointer();
        $descEndToken = $this->tokens[$descEndPtr];

        $this->file->fixer->beginChangeset();

        $this->file->fixer->addContent(
            $descEndPtr,
            $this->file->eolChar . str_repeat('    ', $descEndToken['level']) . ' *'
        );

        $this->file->fixer->endChangeset();
    }

    /**
     * Fixes much lines after description.
     *
     * @param int $startLine Line to start removing
     * @param int $endLine Line to end removing
     *
     * @return void
     */
    private function fixMuchLinesAfterDescription(int $startLine, int $endLine): void
    {
        $this->file->fixer->beginChangeset();

        (new LineHelper($this->file))->removeLines($startLine, $endLine);

        $this->file->fixer->endChangeset();
    }

    /**
     * Fixes the description uc first.
     *
     * @param int $descriptionStartPtr Pointer to the description start
     *
     * @return void
     */
    private function fixDescriptionUcFirst(int $descriptionStartPtr): void
    {
        $descStartToken = $this->tokens[$descriptionStartPtr];

        $this->file->fixer->beginChangeset();

        $this->file->fixer->replaceToken(
            $descriptionStartPtr,
            ucfirst($descStartToken['content'])
        );

        $this->file->fixer->endChangeset();
    }
}
