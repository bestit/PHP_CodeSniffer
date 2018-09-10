<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Class GeneralDocSniff
 *
 * @package BestIt\Sniffs\Commenting
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class GeneralDocSniff implements Sniff
{
    /**
     * Code that there is no line before the comment.
     *
     * @var string
     */
    public const CODE_NO_LINE_BEFORE_COMMENT = 'NoLineBeforeComment';

    /**
     * Message that there is no line before the comment.
     *
     * @var string
     */
    public const MESSAGE_NO_LINE_BEFORE_COMMENT = 'There is no line before the comment.';

    /**
     * Code that are too many lines before comment.
     *
     * @var string
     */
    public const CODE_MANY_LINES_BEFORE_COMMENT = 'ManyLinesBeforeComment';

    /**
     * Message that are too many lines before comment.
     *
     * @var string
     */
    public const MESSAGE_MANY_LINES_BEFORE_COMMENT = 'There are too many lines before the comment.';

    /**
     * Code that are too much spaces between comment tag fragments.
     *
     * @var string
     */
    public const CODE_WRONG_COMMENT_TAG_SPACING = 'WrongCommentTagSpacing';
    
    /**
     * Message that are too much spaces between comment tag fragments.
     *
     * @var string
     */
    public const MESSAGE_WRONG_COMMENT_TAG_SPACING = 'There must only be 1 space between comment tag fragments.';

    /**
     * The PHP_CodeSniffer file where the token was found.
     *
     * @var File CodeSniffer file.
     */
    private $phpcsFile;

    /**
     * The cs file token stack.
     *
     * @var array
     */
    private $tokens;

    /**
     * Pointer of the comment start token.
     *
     * @var int
     */
    private $commentStartPtr;

    /**
     * Token of the comment start
     *
     * @var array
     */
    private $commentStartToken;

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[] List of tokens to listen for
     */
    public function register(): array
    {
        return [
            T_DOC_COMMENT_OPEN_TAG
        ];
    }

    /**
     * Called when one of the token types that this sniff is listening for is found.
     *
     * @param File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int $commentStartPtr The position in the PHP_CodeSniffer file's token stack where the token was found.
     *
     * @return void Optionally returns a stack pointer.
     */
    public function process(File $phpcsFile, $commentStartPtr)
    {
        $this->phpcsFile = $phpcsFile;
        $this->tokens = $phpcsFile->getTokens();
        $this->commentStartPtr = $commentStartPtr;
        $this->commentStartToken = $this->tokens[$commentStartPtr];

        $this->checkEmptyLineBeforeComment();
        $this->checkCommentTagsSpacing();
    }

    /**
     * Checks if an empty line is before the comment.
     *
     * @return void
     */
    private function checkEmptyLineBeforeComment()
    {
        $prevNonSpacePtr = $this->phpcsFile->findPrevious(T_WHITESPACE, $this->commentStartPtr - 1, null, true);
        $prevNonSpaceToken = $this->tokens[$prevNonSpacePtr];

        $hasPrevCurlyBrace = $prevNonSpaceToken['type'] === 'T_OPEN_CURLY_BRACKET';

        $whitespacePtrs = [];
        for ($i = $prevNonSpacePtr + 1; $i < $this->commentStartPtr; $i++) {
            $whitespaceToken = $this->tokens[$i];

            if ($whitespaceToken['column'] === 1 && $this->commentStartToken['line'] !== $whitespaceToken['line']) {
                $whitespacePtrs[] = $i;
            }
        }

        $expectedLines = $hasPrevCurlyBrace ? 0 : 1;

        //More than one line before comment
        if (count($whitespacePtrs) > $expectedLines) {
            $this->addFixableMuchLinesBeforeCommentError($whitespacePtrs, $hasPrevCurlyBrace);

            return;
        }

        if (!$hasPrevCurlyBrace && count($whitespacePtrs) === 0) {
            $this->addFixableNoLineBeforeCommentError();
        }
    }

    /**
     * Check if there is a comment tag alignment.
     *
     * @return void
     */
    private function checkCommentTagsSpacing()
    {
        $commentTagPtrs = $this->commentStartToken['comment_tags'];

        if (count($commentTagPtrs) === 0) {
            return;
        }

        /**
         * Array of comment tag pointers.
         *
         * @var int[] $commentTagPtrs
         */
        foreach ($commentTagPtrs as $commentTagPtr) {
            $this->checkCommentTagSpacing($commentTagPtr);
        }
    }

    /**
     * Checks a single comment tag alignment.
     *
     * @param int $commentTagPtr Pointer to comment tag.
     *
     * @return void
     */
    private function checkCommentTagSpacing(int $commentTagPtr)
    {
        $lineEndingPtr = $this->phpcsFile->findNext(
            T_DOC_COMMENT_WHITESPACE,
            $commentTagPtr,
            null,
            false,
            $this->phpcsFile->eolChar
        );

        for ($tagFragmentPtr = $commentTagPtr; $tagFragmentPtr < $lineEndingPtr; $tagFragmentPtr++) {
            $tagFragmentToken = $this->tokens[$tagFragmentPtr];

            if ($tagFragmentToken['type'] === 'T_DOC_COMMENT_STRING') {
                $this->checkCommentTagStringSpacing($tagFragmentPtr);
            }

            if ($tagFragmentToken['type'] === 'T_DOC_COMMENT_WHITESPACE' && $tagFragmentToken['length'] > 1) {
                $this->checkCommentTagWhiteSpacing($tagFragmentPtr);
            }
        }
    }

    /**
     * Checks comment tag strings spacing.
     *
     * @param int $tagStringPtr Pointer to the beginning of a tag
     *
     * @return void
     */
    private function checkCommentTagStringSpacing(int $tagStringPtr)
    {
        $tagStringToken = $this->tokens[$tagStringPtr];

        $tagStringContent = $tagStringToken['content'];

        if (preg_replace('/\s\s+/', ' ', $tagStringContent) !== $tagStringContent) {
            $fixStringAlignment = $this->phpcsFile->addFixableError(
                self::MESSAGE_WRONG_COMMENT_TAG_SPACING,
                $tagStringPtr,
                self::CODE_WRONG_COMMENT_TAG_SPACING
            );

            if ($fixStringAlignment) {
                $this->fixCommentTagStringSpacing($tagStringPtr);
            }
        }
    }

    /**
     * Fixes comment tag string spacing.
     *
     * @param int $tagStringPtr Pointer to the beginning of a tag
     *
     * @return void
     */
    private function fixCommentTagStringSpacing(int $tagStringPtr)
    {
        $tagStringToken = $this->tokens[$tagStringPtr];
        $tagStringContent = $tagStringToken['content'];

        $this->phpcsFile->fixer->replaceToken(
            $tagStringPtr,
            preg_replace('/\s\s+/', ' ', $tagStringContent)
        );
    }

    /**
     * Fixes too long comment tag whitespaces.
     *
     * @param int $whitespacePtr Pointer to whitespace which is too long.
     *
     * @return void
     */
    private function fixCommentTagSpacing(int $whitespacePtr)
    {
        $this->phpcsFile->fixer->replaceToken($whitespacePtr, ' ');
    }

    /**
     * Fixes lines before comment.
     *
     * @param int[] $whitespacePtrs Pointers of all whitespaces before comment.
     * @param bool $noWhitespace Indicator which controls if there should be minimum one whitespace or not.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    private function fixMuchLinesBeforeComment(array $whitespacePtrs, bool $noWhitespace = false)
    {
        $this->phpcsFile->fixer->beginChangeset();

        foreach ($whitespacePtrs as $whitespaceIndex => $whitespacePtr) {
            if ($whitespaceIndex === 0 && !$noWhitespace) {
                continue;
            }

            $this->phpcsFile->fixer->replaceToken($whitespacePtr, '');
        }

        $this->phpcsFile->fixer->endChangeset();
    }

    /**
     * Fixes no line before comment.
     *
     * @return void
     */
    private function fixNoLineBeforeComment()
    {
        $this->phpcsFile->fixer->beginChangeset();

        $offset = $this->commentStartToken['column'] === 1 ? 0 : 1;

        $this->phpcsFile->fixer->addNewlineBefore($this->commentStartPtr - $offset);

        $this->phpcsFile->fixer->endChangeset();
    }

    /**
     * Checks given whitespace for proper spacing.
     *
     * @param int $whitespacePtr Pointer to whitespace to check.
     *
     * @return void
     */
    private function checkCommentTagWhiteSpacing(int $whitespacePtr)
    {
        $fixWrongWhitespace = $this->phpcsFile->addFixableError(
            self::MESSAGE_WRONG_COMMENT_TAG_SPACING,
            $whitespacePtr,
            self::CODE_WRONG_COMMENT_TAG_SPACING
        );

        if ($fixWrongWhitespace) {
            $this->fixCommentTagSpacing($whitespacePtr);
        }
    }

    /**
     * Adds the fixable much lines before comment error.
     *
     * @param int[] $whitespacePtrs All whitespace pointers
     * @param bool $hasPrevCurlyBrace Indicator if there is a previous curly brace
     *
     * @return void
     */
    private function addFixableMuchLinesBeforeCommentError(array $whitespacePtrs, bool $hasPrevCurlyBrace)
    {
        $fixMuchLines = $this->phpcsFile->addFixableError(
            self::MESSAGE_MANY_LINES_BEFORE_COMMENT,
            $this->commentStartPtr,
            self::CODE_MANY_LINES_BEFORE_COMMENT
        );

        if ($fixMuchLines) {
            $this->fixMuchLinesBeforeComment(
                $whitespacePtrs,
                $hasPrevCurlyBrace
            );
        }
    }

    /**
     * Adds the fixable no line before comment error.
     *
     * @return void
     */
    private function addFixableNoLineBeforeCommentError()
    {
        $fixNoLine = $this->phpcsFile->addFixableError(
            self::MESSAGE_NO_LINE_BEFORE_COMMENT,
            $this->commentStartPtr,
            self::CODE_NO_LINE_BEFORE_COMMENT
        );

        if ($fixNoLine) {
            $this->fixNoLineBeforeComment();
        }
    }
}
