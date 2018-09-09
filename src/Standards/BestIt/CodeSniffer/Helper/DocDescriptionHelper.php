<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use BestIt\CodeSniffer\File;
use BestIt\Sniffs\Commenting\AbstractDocSniff;

/**
 * Class DocDescriptionHelper
 *
 * @package BestIt\Helper
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
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
    public function checkCommentDescription(bool $descriptionRequired)
    {
        $this->descriptionRequired = $descriptionRequired;

        $commentStartToken = $this->docHelper->getCommentStartToken();
        $commentEndToken = $this->docHelper->getCommentEndToken();

        $summaryPtr = $this->summaryHelper->getCommentSummaryPointer();

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
        $this->checkCommentDescriptionLineLength($descriptionStartPtr, $descriptionEndPtr);

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
    private function checkCommentDescriptionUcFirst(int $descriptionStartPtr)
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
     * Checks the line length of each line of the comment description.
     *
     * @param int $descriptionStartPtr Pointer to the start of the description.
     * @param int $descriptionEndPtr Pointer to the end of the description.
     *
     * @return void
     */
    private function checkCommentDescriptionLineLength(int $descriptionStartPtr, int $descriptionEndPtr)
    {
        $diffTokens = array_slice(
            $this->tokens,
            $descriptionStartPtr,
            $descriptionEndPtr - $descriptionStartPtr + 2,
            true
        );

        foreach ($diffTokens as $diffToken) {
            if ($diffToken['type'] !== 'T_DOC_COMMENT_WHITESPACE') {
                continue;
            }

            if ($diffToken['content'] !== $this->file->getEolChar()) {
                continue;
            }

            if ($diffToken['column'] > AbstractDocSniff::MAX_LINE_LENGTH) {
                $this->file->addErrorOnLine(
                    AbstractDocSniff::MESSAGE_DESCRIPTION_TOO_LONG,
                    $diffToken['line'],
                    AbstractDocSniff::CODE_DESCRIPTION_TOO_LONG
                );
            }
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

        $commentStartToken = $this->docHelper->getCommentStartToken();
        $commentEndPtr = $this->docHelper->getCommentEndPointer();

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
        $commentStartToken = $this->docHelper->getCommentStartToken();
        $commentEndPtr = $this->docHelper->getCommentEndPointer();

        $summaryPtr = $this->summaryHelper->getCommentSummaryPointer();

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
    private function addDescriptionNotFoundError(int $summaryPtr)
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
    private function fixNoLineAfterDescription()
    {
        $descEndPtr = $this->getCommentDescriptionEndPointer();
        $descEndToken = $this->tokens[$descEndPtr];

        $this->file->getFixer()->beginChangeset();

        $this->file->getFixer()->addContent(
            $descEndPtr,
            $this->file->getEolChar() . str_repeat('    ', $descEndToken['level']) . ' *'
        );

        $this->file->getFixer()->endChangeset();
    }

    /**
     * Fixes much lines after description.
     *
     * @param int $startLine Line to start removing
     * @param int $endLine Line to end removing
     *
     * @return void
     */
    private function fixMuchLinesAfterDescription(int $startLine, int $endLine)
    {
        $this->file->getFixer()->beginChangeset();

        $this->file->getFixer()->removeLines($startLine, $endLine);

        $this->file->getFixer()->endChangeset();
    }

    /**
     * Fixes the description uc first.
     *
     * @param int $descriptionStartPtr Pointer to the description start
     *
     * @return void
     */
    private function fixDescriptionUcFirst(int $descriptionStartPtr)
    {
        $descStartToken = $this->tokens[$descriptionStartPtr];

        $this->file->getFixer()->beginChangeset();

        $this->file->getFixer()->replaceToken(
            $descriptionStartPtr,
            ucfirst($descStartToken['content'])
        );

        $this->file->getFixer()->endChangeset();
    }
}
