<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\Helper\DocTagHelper;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\DocPosProviderTrait;
use Closure;
use function array_filter;
use function array_key_exists;
use function array_map;
use function array_walk;
use function count;
use function in_array;
use function is_callable;
use function sprintf;
use function substr;
use function ucfirst;

/**
 * Abstract sniff for the the required tags of a php structure.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
abstract class AbstractRequiredTagsSniff extends AbstractSniff
{
    use DocPosProviderTrait;

    /**
     * Code that comment tag must appear maximum x times.
     *
     * @var string
     */
    public const CODE_TAG_OCCURRENCE_MAX_PREFIX = 'TagOccurrenceMax';

    /**
     * Code that comment tag must appear minimum x times.
     *
     * @var string
     */
    public const CODE_TAG_OCCURRENCE_MIN_PREFIX = 'TagOccurrenceMin';

    /**
     * Message that comment tag must appear maximum x times.
     *
     * @var string
     */
    private const MESSAGE_TAG_OCCURRENCE_MAX = 'The comment tag "%s" must appear maximum %d times. Found %d times.';

    /**
     * Message that comment tag must appear minimum x times.
     *
     * @var string
     */
    private const MESSAGE_TAG_OCCURRENCE_MIN = 'The comment tag "%s" must appear minimum %d times. Found %d times.';

    /**
     * Caches the processed tag rules.
     *
     * @var array|null
     */
    private $processedTagRules = null;

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
        return (bool) $this->getDocCommentPos();
    }

    /**
     * Loads the rules with amax rule and checks if there are more tags than allowed.
     *
     * @return void
     */
    private function checkAndRegisterTagMaximumCounts(): void
    {
        $allTags = $this->getAllTags();
        $checkedTags = [];
        $file = $this->getFile();
        $tagRules = $this->getRulesWithRequirement('max');

        array_walk(
            $allTags,
            function (array $tag, int $tagPos) use (&$checkedTags, $file, $tagRules): void {
                $tagContent = substr($tag['content'], 1);

                if (!in_array($tagContent, $checkedTags)) {
                    $checkedTags[] = $tagContent;

                    $tagCount = count($this->findTokensForTag($tagContent));
                    $maxCount = @$tagRules[$tagContent]['max'] ?? 0;

                    if ($maxCount && ($tagCount > $maxCount)) {
                        $file->recordMetric(
                            $tagPos,
                            sprintf('Tags on %s occurred to often', $this->token['type']),
                            $tagContent
                        );

                        $file->addError(
                            self::MESSAGE_TAG_OCCURRENCE_MAX,
                            $tagPos,
                            // We use an error code containing the tag name because we can't configure this rules from
                            // the outside and need  specific code to exclude the rule for this special tag.
                            self::CODE_TAG_OCCURRENCE_MAX_PREFIX . ucfirst($tagContent),
                            [
                                $tagContent,
                                $maxCount,
                                $tagCount
                            ]
                        );
                    }
                }
            }
        );
    }

    /**
     * Checks if the tag occurrences reaches their minimum counts.
     *
     * @return void
     */
    private function checkAndRegisterTagMinimumCounts(): void
    {
        $checkedRule = 'min';
        $docPos = $this->getDocCommentPos();
        $file = $this->getFile();
        $rulesWithReq = $this->getRulesWithRequirement($checkedRule);

        array_walk(
            $rulesWithReq,
            function (array $tagRule, string $tag) use ($checkedRule, $docPos, $file): void {
                $minCount = $tagRule[$checkedRule];
                $tagCount = count($this->findTokensForTag($tag));

                if ($minCount > $tagCount) {
                    $file->recordMetric(
                        $docPos,
                        sprintf('Tags on %s occurred not often enough', $this->token['type']),
                        $tag
                    );

                    $file->addError(
                        self::MESSAGE_TAG_OCCURRENCE_MIN,
                        $docPos,
                        // We use an error code containing the tag name because we can't configure this rules from the
                        // outside and need  specific code to exclude the rule for this special tag.
                        self::CODE_TAG_OCCURRENCE_MIN_PREFIX . ucfirst($tag),
                        [
                            $tag,
                            $minCount,
                            $tagCount
                        ]
                    );
                }
            }
        );
    }

    /**
     * Returns only the tokens with the given tag name.
     *
     * @param string $tagName
     *
     * @return array
     */
    private function findTokensForTag(string $tagName): array
    {
        $allTags = $this->getAllTags();

        return array_filter($allTags, function (array $tag) use ($tagName): bool {
            return substr($tag['content'], 1) === $tagName;
        });
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
     * Returns the rules with their resolved callbacks.
     *
     * @return array
     */
    private function getProcessedTagRules(): array
    {
        if ($this->processedTagRules === null) {
            $this->processedTagRules = $this->processTagRules();
        }

        return $this->processedTagRules;
    }

    /**
     * Returns the rules with the given requirement.
     *
     * @param string $requiredRule
     *
     * @return array
     */
    private function getRulesWithRequirement(string $requiredRule): array
    {
        $processedTagRules = $this->getProcessedTagRules();

        $processedTagRules = array_filter($processedTagRules, function (array $tagRule) use ($requiredRule): bool {
            return array_key_exists($requiredRule, $tagRule);
        });
        return $processedTagRules;
    }

    /**
     * Returns the required tag data.
     *
     * The order in which they appear in this array os the order for tags needed.
     *
     * @return array List of tag metadata
     */
    abstract protected function getTagRules(): array;

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
     * Resolves the callbacks in the tag rules.
     *
     * @return array
     */
    private function processTagRules(): array
    {
        $processedTagRules = $this->getTagRules();

        array_walk($processedTagRules, function (&$tagRule) {
            $tagRule = array_map(function ($valueOrCallback) {
                return is_callable($valueOrCallback, true)
                    ? Closure::fromCallable($valueOrCallback)->call($this)
                    : $valueOrCallback;
            }, $tagRule);
        });

        return $processedTagRules;
    }

    /**
     * Processes a found registered token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this->checkAndRegisterTagMaximumCounts();
        $this->checkAndRegisterTagMinimumCounts();
    }

    /**
     * Resets the cached data.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->resetDocCommentPos();

        $this->processedTagRules = null;
        $this->tags = null;
    }
}
