<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\CodeWarning;
use function explode;
use function in_array;
use function strtolower;

/**
 * Class ReturnTagSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class ReturnTagSniff extends AbstractTagSniff
{
    /**
     * You SHOULD provide a description your return.
     *
     * It is disabled per default. You can enabled it per property "descAsWarning".
     */
    public const CODE_MISSING_RETURN_DESC = 'MissingReturnDescription';

    /**
     * You SHOULD provide a native return and prevent "mixed".
     */
    public const CODE_MIXED_TYPE = 'MixedType';

    /**
     * Message that the tag content format is invalid.
     */
    private const MESSAGE_MISSING_RETURN_DESC = 'Are you sure that you do not want to describe your return?';

    /**
     * The message for the mixed type warning.
     */
    private const MESSAGE_MIXED_TYPE = 'We suggest that you avoid the "mixed" type and declare the ' .
        'required types in detail.';

    /**
     * Should the missing description emit a warning?
     *
     * @var bool
     */
    public $descAsWarning = false;

    /**
     * This return types will not need a summary in any case.
     *
     * @var array
     */
    public $excludedTypes = ['void'];

    /**
     * Throws a code warning if you have no description.
     *
     * @throws CodeWarning
     *
     * @param string $type
     * @param array $returnParts
     *
     * @return void
     */
    private function checkForMissingDesc(string $type, array $returnParts): void
    {
        if (!in_array($type, $this->excludedTypes) && (count($returnParts) <= 1) && $this->descAsWarning) {
            throw (new CodeWarning(
                static::CODE_MISSING_RETURN_DESC,
                self::MESSAGE_MISSING_RETURN_DESC,
                $this->stackPos
            ))->setToken($this->token);
        }
    }

    /**
     * Throws a warning if you declare a mixed type.
     *
     * @param string $type
     * @throws CodeWarning
     *
     * @return $this
     */
    private function checkForMixedType(string $type): self
    {
        if (strtolower($type) === 'mixed') {
            throw (new CodeWarning(static::CODE_MIXED_TYPE, self::MESSAGE_MIXED_TYPE, $this->stackPos))
                ->setToken($this->token);
        }

        return $this;
    }

    /**
     * Processed the content of the required tag.
     *
     * @param null|string $tagContent The possible tag content or null.
     * @throws CodeWarning If there is a mixed type.
     *
     * @return void
     */
    protected function processTagContent(?string $tagContent = null): void
    {
        $returnParts = explode(' ', (string) $tagContent);
        $type = $returnParts[0];

        $this
            ->checkForMixedType($type)
            ->checkForMissingDesc($type, $returnParts);
    }

    /**
     * For which tag should be sniffed?
     *
     * @return string The name of the tag without the "@"-prefix.
     */
    protected function registerTag(): string
    {
        return 'return';
    }
}
