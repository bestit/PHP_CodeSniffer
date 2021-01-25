<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing;

use SlevomatCodingStandard\Sniffs\Classes\ConstantSpacingSniff as BaseSniff;

/**
 * Constants must be separated by a line.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Spacing
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ConstantSpacingSniff extends BaseSniff
{
    /**
     * There MUST be a line even without comments.
     *
     * @var int
     */
    // phpcs:ignore BestIt.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    public $minLinesCountBeforeWithoutComment = 1;
}
