<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\AbstractSniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function class_exists;
use function implode;
use function spl_autoload_call;
use function substr;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;

// Get the custom constants.
if (!class_exists(Tokens::class, false)) {
    spl_autoload_call(Tokens::class);
}

/**
 * The basic sniff for tags.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
abstract class AbstractTagSniff extends AbstractSniff
{
    /**
     * You MUST provide a special tag format.
     */
    public const CODE_TAG_CONTENT_FORMAT_INVALID = 'TagContentFormatInvalid';

    /**
     * Message that the tag content format is invalid.
     */
    protected const MESSAGE_TAG_CONTENT_FORMAT_INVALID = '%s is invalid. Expected format: "%s"';

    /**
     * Returns true if there is a matching tag.
     *
     * @return bool
     */
    protected function areRequirementsMet(): bool
    {
        return (substr($this->tokens[$this->stackPos]['content'], 1) === $this->registerTag());
    }

    /**
     * Loads the tag content for the given tag position.
     *
     * @return null|string The content of the tag comment or null if there is nothing.
     */
    private function loadTagContent(): ?string
    {
        $contents = [];
        $nextOrClosingPos = $this->file->findNext([T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_TAG], $this->stackPos + 1);
        $positionsTillEnd = TokenHelper::findNextAll($this->file->getBaseFile(), [T_DOC_COMMENT_STRING], $this->stackPos, $nextOrClosingPos);

        foreach ($positionsTillEnd as $position) {
            $contents[] = $this->tokens[$position]['content'];
        }

        return $contents ? implode(' ', $contents) : null;
    }

    /**
     * Processed the content of the required tag.
     *
     * @param null|string $tagContent The possible tag content or null.
     *
     * @return void
     */
    abstract protected function processTagContent(?string $tagContent = null): void;

    /**
     * Processes a found registered token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this->processTagContent($this->loadTagContent());
    }

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[] Listens to the tags.
     */
    public function register(): array
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    abstract protected function registerTag(): string;
}
