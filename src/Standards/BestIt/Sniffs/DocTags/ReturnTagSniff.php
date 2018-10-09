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
     * Code that the tag content format is invalid.
     *
     * @var string
     */
    public const CODE_TAG_MISSING_RETURN_DESC = 'MissingReturnDescription';

    /**
     * Error code for the mixed type.
     *
     * @var string
     */
    public const CODE_TAG_MIXED_TYPE = 'MixedType';

    /**
     * Message that the tag content format is invalid.
     *
     * @var string
     */
    private const MESSAGE_CODE_TAG_MISSING_RETURN_DESC = 'Are you sure that you do not want to describe your return?';

    /**
     * The message for the mixed type warning.
     *
     * @var string
     */
    private const MESSAGE_TAG_MIXED_TYPE = 'We suggest that you avoid the "mixed" type and declare the ' .
        'required types in detail.';

    /**
     * This return types will not need a summary in any case.
     *
     * @var array
     */
    public $excludedTypes = ['void'];

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

        if (strtolower($type) === 'mixed') {
            $this->getFile()->recordMetric(
                $this->getStackPos(),
                sprintf('Valid %s tag:', $this->registerTag()),
                'mixed'
            );

            throw (new CodeWarning(static::CODE_TAG_MIXED_TYPE, self::MESSAGE_TAG_MIXED_TYPE, $this->stackPos))
                ->setToken($this->token);
        }

        if (!in_array($type, $this->excludedTypes) && count($returnParts) <= 1) {
            $this->getFile()->recordMetric(
                $this->getStackPos(),
                sprintf('Valid %s tag:', $this->registerTag()),
                'No'
            );

            $this->file->addWarning(
                self::MESSAGE_CODE_TAG_MISSING_RETURN_DESC,
                $this->stackPos,
                static::CODE_TAG_MISSING_RETURN_DESC
            );
        } else {
            $this->getFile()->recordMetric(
                $this->getStackPos(),
                sprintf('Valid %s tag:', $this->registerTag()),
                'Yes'
            );
        }
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
