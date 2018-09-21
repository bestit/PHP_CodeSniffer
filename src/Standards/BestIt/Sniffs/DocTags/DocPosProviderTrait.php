<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\File;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_merge;
use const T_DOC_COMMENT_OPEN_TAG;

/**
 * Trait DocPosProviderTrait.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
trait DocPosProviderTrait
{
    /**
     * The position of the doc comment or null.
     *
     * @var int|null
     */
    private $docCommentPos = -1;

    /**
     * The used file.
     *
     * @var File|void
     */
    protected $file;

    /**
     * Returns the position of the doc block if there is one.
     *
     * @return int|null
     */
    protected function getDocCommentPos(): ?int
    {
        if ($this->docCommentPos === -1) {
            $this->docCommentPos = $this->loadDocCommentPos();
        }

        return $this->docCommentPos;
    }

    /**
     * Loads the position of the doc comment.
     *
     * @return int|null
     */
    protected function loadDocCommentPos(): ?int
    {
        $docCommentPos = (int) $this->file->findPrevious(
            [T_DOC_COMMENT_OPEN_TAG],
            $this->stackPos,
            TokenHelper::findPreviousExcluding(
                $this->file->getBaseFile(),
                array_merge(TokenHelper::$ineffectiveTokenCodes, Tokens::$methodPrefixes),
                $this->stackPos - 1
            )
        );

        return $docCommentPos > 0 ? $docCommentPos : null;
    }
}
