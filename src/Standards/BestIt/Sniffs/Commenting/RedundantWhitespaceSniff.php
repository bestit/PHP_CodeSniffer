<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\AbstractSniff;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;

/**
 * Sniffs for redundant whitespace in doc blocks.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class RedundantWhitespaceSniff extends AbstractSniff
{
    /**
     * The error code.
     *
     * @var string
     */
    public const CODE_ERROR_REDUNDANT_WHITESPACE = 'RedundantWhitespace';

    /**
     * The message for the user.
     *
     * @var string
     */
    private const MESSAGE_ERROR_REDUNDANT_WHITESPACE = 'Please remove unnecessary whitespace.';

    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $file = $this->getFile();
        $stackPos = $this->getStackPos();
        $nextTokenContentPos = $file->findNext(
            [T_DOC_COMMENT_WHITESPACE],
            $stackPos + 1,
            null,
            true
        );

        $nextToken = $this->tokens[$nextTokenContentPos];

        if (($nextToken['code'] === T_DOC_COMMENT_TAG) && ($this->token['column'] + 2 !== $nextToken['column'])) {
            // This thing is not fixable, because the codesniffer - as you can see - does not parse the whitespace as
            // a token but handles it as "columns"
            $file->addError(
                self::MESSAGE_ERROR_REDUNDANT_WHITESPACE,
                $stackPos,
                static::CODE_ERROR_REDUNDANT_WHITESPACE
            );
        }
    }

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * An example return value for a sniff that wants to listen for whitespace
     * and any comments would be:
     *
     * <code>
     *    return array(
     *            T_WHITESPACE,
     *            T_DOC_COMMENT,
     *            T_COMMENT,
     *           );
     * </code>
     *
     * @return int[]
     * @see    Tokens.php
     */
    public function register(): array
    {
        return [T_DOC_COMMENT_STAR];
    }
}
