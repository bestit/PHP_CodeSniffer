<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use DomainException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use const T_DOC_COMMENT_CLOSE_TAG;

/**
 * Class DocHelper
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class DocHelper
{
    /**
     * The position of the end token of the doc block.
     *
     * @var int|null|bool If false then it will be loaded in the getter.
     */
    private $blockEndPosition = false;

    /**
     * The php cs file.
     *
     * @var File
     */
    private $file;

    /**
     * Position to the token which is to be listened.
     *
     * @var int
     */
    private $stackPos;

    /**
     * Token stack of the current file.
     *
     * @var array
     */
    private $tokens;

    /**
     * DocHelper constructor.
     *
     * @param File $file File object of file which is processed.
     * @param int $stackPos Position to the token which is processed.
     */
    public function __construct(File $file, int $stackPos)
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();
        $this->stackPos = $stackPos;
    }

    /**
     * Returns position to the class comment end.
     *
     * @return int|null Position to the class comment end.
     */
    public function getBlockEndPosition(): ?int
    {
        if ($this->blockEndPosition === false) {
            $this->blockEndPosition = $this->loadBlockEndPosition();
        }

        return $this->blockEndPosition;
    }

    /**
     * Returns token data of the evaluated class comment end.
     *
     * @return array Token data of the comment end.
     */
    public function getBlockEndToken(): array
    {
        if (!$this->hasDocBlock()) {
            throw new DomainException(
                sprintf('Missing doc block for position %s of file %s.', $this->stackPos, $this->file->getFilename())
            );
        }

        return $this->tokens[$this->getBlockEndPosition()];
    }

    /**
     * Returns pointer to the class comment start.
     *
     * @return int Pointer to the class comment start.
     */
    public function getBlockStartPosition(): int
    {
        $commentEndToken = $this->getBlockEndToken();

        return $commentEndToken['comment_opener'];
    }

    /**
     * Returns token data of the evaluated class comment start.
     *
     * @return array Token data of the comment start.
     */
    public function getBlockStartToken(): array
    {
        $commentStartPtr = $this->getBlockStartPosition();

        return $this->tokens[$commentStartPtr];
    }

    /**
     * Returns true if there is a doc block.
     *
     * @return bool
     */
    public function hasDocBlock(): bool
    {
        return $this->getBlockEndPosition() !== null;
    }

    /**
     * Returns true if this doc block is a multi line comment.
     *
     * @return bool
     */
    public function isMultiLine(): bool
    {
        $openingToken = $this->getBlockStartToken();
        $closingToken = $this->getBlockEndToken();

        return $openingToken['line'] < $closingToken['line'];
    }

    /**
     * Returns the position of the token for the doc block end.
     *
     * @return int|null
     */
    private function loadBlockEndPosition(): ?int
    {
        $endPos = $this->file->findPrevious(
            [T_DOC_COMMENT_CLOSE_TAG],
            $this->stackPos - 1,
            // Search till the next method, property, etc ...
            TokenHelper::findPreviousExcluding(
                $this->file,
                TokenHelper::$ineffectiveTokenCodes + Tokens::$methodPrefixes,
                $this->stackPos - 1
            )
        );

        return ((int) $endPos) > 0 ? $endPos : null;
    }
}
