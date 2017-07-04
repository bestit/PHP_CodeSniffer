<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;

/**
 * This Sniff checks the method phpdoc
 *
 * It is a modified version of the Generic DocCommentSniff adjusted for the bestit codestyle
 *
 * @package BestIt\Sniffs\Commenting
 * @author Nils Hardeweg <nils.hardeweg@bestit-online.de>
 */
class MethodDocSniff implements PHP_CodeSniffer_Sniff
{
    #region constants
    /**
     * Code for an empty doc block
     *
     * @var string
     */
    const CODE_EMPTY = 'Empty';

    /**
     * Message for an empty doc block
     *
     * @var string
     */
    const MESSAGE_DOC_EMPTY = 'Doc comment is empty';

    /**
     * Code for a Spacing Error after the Summary
     *
     * @var string
     */
    const CODE_SPACING_AFTER = 'SpacingAfter';

    /**
     * Message for a Spacing Error after the Summary
     *
     * @var string
     */
    const MESSAGE_SPACING_AFTER = 'Additional blank lines found at end of doc comment';

    /**
     * Code for missing spacing between descriptions
     *
     * @var string
     */
    const CODE_SPACING_BETWEEN = 'SpacingBetween';

    /**
     * Message for missing spacing between descriptions
     *
     * @var string
     */
    const MESSAGE_SPACING_BETWEEN = 'There must be exactly one blank line between descriptions in a doc comment';

    /**
     * Code for missing spacing before the tags
     *
     * @var string
     */
    const CODE_SPACING_BEFORE_TAGS = 'SpacingBeforeTags';

    /**
     * Message for missing spacing before the tags
     *
     * @var string
     */
    const MESSAGE_SPACING_BEFORE_TAGS = 'There must be exactly one blank line before the tags in a doc comment';

    /**
     * Code for missing short description (Summary)
     *
     * @var string
     */
    const CODE_MISSING_SHORT = 'MissingShort';

    /**
     * Message for missing short description (Summary)
     *
     * @var string
     */
    const MESSAGE_MISSING_SHORT = 'Missing short description in doc comment';

    /**
     * Code for not capitalized short description
     *
     * @var string
     */
    const CODE_SHORT_NOT_CAPITAL = 'ShortNotCapital';

    /**
     * Message for not capitalized short description
     *
     * @var string
     */
    const MESSAGE_SHORT_NOT_CAPITAL = 'Doc comment short description must start with a capital letter';

    /**
     * Code for not Capitalized long description
     *
     * @var string
     */
    const CODE_LONG_NOT_CAPITAL = 'LongNotCapital';

    /**
     * Message for not Capitalized long description
     *
     * @var string
     */
    const MESSAGE_LONG_NOT_CAPITAL = 'Doc comment long description must start with a capital letter';

    /**
     * Code for empty lines before the short description
     *
     * @var string
     */
    const CODE_SPACING_BEFORE_SHORT = 'SpacingBeforeShort';

    /**
     * Message for empty lines before the short description
     *
     * @var string
     */
    const MESSAGE_CODE_SPACING_BEFORE = 'Doc comment short description must be on the first line';
    #endregion

    #region Properties
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = ['PHP', 'JS'];

    /**
     * The token array is the base of this evaluation
     *
     * @var array
     */
    public $tokens;

    /**
     * Definitions for an empty doc block
     *
     * @var array
     */
    public $empty = [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR];

    /**
     * The phpcs file
     *
     * @var PHP_CodeSniffer_File
     */
    public $phpcsFile;

    /**
     * Pointer to the start of the comment
     *
     * @var int
     */
    public $commentStart;

    /**
     * Pointer to the start of the short description
     *
     * @var int
     */
    public $short;
    #endregion

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register(): array
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->tokens = $phpcsFile->getTokens();
        $this->phpcsFile = $phpcsFile;

        $this->commentStart = $stackPtr;
        $commentEnd = $this->tokens[$stackPtr]['comment_closer'];

        $this->short = $this->phpcsFile->findNext($this->empty, $stackPtr + 1, $commentEnd, true);

        if ($this->short === false) {
            // No content at all.
            $phpcsFile->addError(self::MESSAGE_DOC_EMPTY, $stackPtr, self::CODE_EMPTY);
            return;
        }
        $this->checkMethodPhpDoc(
            $stackPtr,
            $commentEnd
        );
    }

    /**
     * Checks if there is a blank line at the end of the doc
     *
     * @param $commentEnd
     * @param $prev
     *
     * @return void
     */
    private function checkBlankLines($commentEnd, $prev)
    {
        // Check for additional blank lines at the end of the comment.
        if ($this->tokens[$prev]['line'] < ($this->tokens[$commentEnd]['line'] - 1)) {
            $this->phpcsFile->addError(self::MESSAGE_SPACING_AFTER, $commentEnd, self::CODE_SPACING_AFTER);
        }
    }

    /**
     * Checks if short or long description starts with a capital letter
     *
     * @param $shortContent
     * @param $long
     *
     * @return void
     */
    private function checkCapitalLetter($shortContent, $long)
    {
        if (preg_match('/^\p{Ll}/u', $shortContent) === 1) {
            $this->phpcsFile->addError(self::MESSAGE_SHORT_NOT_CAPITAL, $this->short, self::CODE_SHORT_NOT_CAPITAL);
        }

        if ($long !== false
            && $this->tokens[$long]['code'] === T_DOC_COMMENT_STRING
            && preg_match('/^\p{Ll}/u', $this->tokens[$long]['content']) === 1
        ) {
            $this->phpcsFile->addError(self::MESSAGE_LONG_NOT_CAPITAL, $long, self::CODE_LONG_NOT_CAPITAL);
        }
    }

    /**
     * Checks capital letter and spacing of long description
     *
     * @param $shortEnd
     * @param $long
     *
     * @return void
     */
    private function checkLongDescription(
        $shortEnd,
        $long
    ) {
        if ($long !== false && $this->tokens[$long]['code'] === T_DOC_COMMENT_STRING) {
            if ($this->tokens[$long]['line'] !== ($this->tokens[$shortEnd]['line'] + 2)) {
                $this->phpcsFile->addError(self::MESSAGE_SPACING_BETWEEN, $long, self::CODE_SPACING_BETWEEN);
            }

            if (preg_match('/^\p{Ll}/u', $this->tokens[$long]['content']) === 1) {
                $this->phpcsFile->addError(self::MESSAGE_LONG_NOT_CAPITAL, $long, self::CODE_LONG_NOT_CAPITAL);
            }
        }
    }

    /**
     * Checks for a blank line before the doc tags
     *
     * @param $stackPtr
     *
     * @return void
     */
    private function checkCommentTags($stackPtr)
    {
        if (!empty($this->tokens[$this->commentStart]['comment_tags'])) {
            $firstTag = $this->tokens[$this->commentStart]['comment_tags'][0];
            $prev = $this->phpcsFile->findPrevious($this->empty, $firstTag - 1, $stackPtr, true);

            if ($this->tokens[$this->short]['content'] !== '@inheritdoc'
                && $this->tokens[$this->short]['content'] !== '@var'
                && $this->tokens[$firstTag]['line'] !== ($this->tokens[$prev]['line'] + 2)
            ) {
                $this
                    ->phpcsFile
                    ->addError(self::MESSAGE_SPACING_BEFORE_TAGS, $firstTag, self::CODE_SPACING_BEFORE_TAGS);
            }
        }
    }

    /**
     * Checks long and short description
     *
     * @param $stackPtr
     * @param $commentEnd
     *
     * @return void
     */
    private function checkSummaryAndLongDescription($stackPtr, $commentEnd)
    {
        // No extra newline before short description.
        if ($this->tokens[$this->short]['content'] !== '@var'
            && $this->tokens[$this->short]['line'] !== ($this->tokens[$stackPtr]['line'] + 1)) {
            $this->phpcsFile->addError(
                self::MESSAGE_CODE_SPACING_BEFORE,
                $this->short,
                self::CODE_SPACING_BEFORE_SHORT
            );
        }

        $shortContent = $this->tokens[$this->short]['content'];
        /** @var int $shortEnd */
        $shortEnd = $this->short;

        if (preg_match('/^\p{Ll}/u', $shortContent) === 1) {
            $this->phpcsFile->addError(self::MESSAGE_SHORT_NOT_CAPITAL, $this->short, self::CODE_SHORT_NOT_CAPITAL);
        }

        $long = $this->phpcsFile->findNext($this->empty, $shortEnd + 1, $commentEnd - 1, true);

        $this->checkCapitalLetter($shortContent, $long);

        $long = $this->phpcsFile->findNext($this->empty, $shortEnd + 1, $commentEnd - 1, true);

        $this->checkLongDescription($shortEnd, $long);

        $this->checkCommentTags($stackPtr);
    }

    /**
     * Checks the method doc block
     *
     * @param $stackPtr
     * @param $commentEnd
     *
     * @return void
     */
    private function checkMethodPhpDoc($stackPtr, $commentEnd)
    {
        // The last line of the comment should just be the */ code.
        $prev = $this->phpcsFile->findPrevious($this->empty, $commentEnd - 1, $stackPtr, true);

        $this->checkBlankLines($commentEnd, $prev);

        // Check for a comment description.
        if ($this->tokens[$this->short]['code'] !== T_DOC_COMMENT_STRING
            && $this->tokens[$this->short]['content'] !== '@inheritdoc'
            && $this->tokens[$this->short]['content'] !== '@var'
        ) {
            $error = self::MESSAGE_MISSING_SHORT;
            $this->phpcsFile->addError($error, $stackPtr, self::CODE_MISSING_SHORT);
            return;
        }
        $this->checkSummaryAndLongDescription($stackPtr, $commentEnd);
    }
}
