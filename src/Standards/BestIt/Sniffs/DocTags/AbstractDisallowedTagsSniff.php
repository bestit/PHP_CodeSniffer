<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\Helper\DocTagHelper;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\DocPosProviderTrait;
use function in_array;
use function substr;

/**
 * Sniff to disallow the given tags.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
abstract class AbstractDisallowedTagsSniff extends AbstractSniff
{
    use DocPosProviderTrait;

    /**
     * Code that comment tag is not allowed.
     *
     * @var string
     */
    public const CODE_TAG_NOT_ALLOWED = 'TagNotAllowed';

    /**
     * Message that comment tag is not allowed.
     *
     * @var string
     */
    private const MESSAGE_TAG_NOT_ALLOWED = 'The comment tag "%s" is not allowed.';

    /**
     * This tags are disallowed and could be injected from the outside.
     *
     * @var array
     */
    public $disallowedTags = [];

    /**
     * The possible tags of this php structure.
     *
     * @var array|null Tag tokens.
     */
    private $tags = null;

    /**
     * Returns true if the requirements for this sniff are met.
     *
     * @return bool Are the requirements met and the sniff should proceed?
     */
    protected function areRequirementsMet(): bool
    {
        return $this->getDocCommentPos() && $this->getAllTags();
    }

    /**
     * Checks and registers errors for the disallowed tags.
     *
     * @return void
     */
    private function checkAndRegisterDisallowedTagsError(): void
    {
        $disallowedTags = $this->getDisallowedTags();
        $tags = $this->getAllTags();

        foreach ($tags as $tagPos => $tag) {
            $tagContent = $tag['content'];

            if (in_array(substr($tagContent, 1), $disallowedTags)) {
                $this->file->addError(
                    self::MESSAGE_TAG_NOT_ALLOWED,
                    $tagPos,
                    self::CODE_TAG_NOT_ALLOWED,
                    [$tagContent]
                );
            }
        }
    }

    /**
     * Returns all tag tokens for this doc block.
     *
     * @return array
     */
    private function getAllTags(): array
    {
        if ($this->tags === null) {
            $this->tags = $this->loadAllTags();
        }

        return $this->tags;
    }

    /**
     * Type-safe getter for the disallowed tags.
     *
     * We need this because the ruleset user can "break" the api, if he does not provide an array with his config.
     *
     * @return array
     */
    private function getDisallowedTags(): array
    {
        return $this->disallowedTags;
    }

    /**
     * Loads all tags of the structures doc block.
     *
     * @return array
     */
    private function loadAllTags(): array
    {
        return (new DocTagHelper(
            $this->tokens[$this->getDocCommentPos()],
            $this->file,
            $this->stackPos
        )
        )->getCommentTagTokens();
    }

    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this->checkAndRegisterDisallowedTagsError();
    }

    /**
     * Removes the cached data.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->resetDocCommentPos();

        $this->tags = null;
    }
}
