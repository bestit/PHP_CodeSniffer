<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff as SlevomatPropertyTypeHintSniff;

/**
 * Class PropertyTypeHintSniff
 *
 * @author Stefan Haeling <stefan.haeling@bestit.de>
 * @package BestIt\Sniffs\TypeHints
 */
class PropertyTypeHintSniff extends SlevomatPropertyTypeHintSniff
{
    /**
     * Enforces to transform @var int into native int typehint.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var bool bool/null
     */
    public $enableNativeTypeHint = true;

    /**
     * Enforces to transform @var mixed into native mixed typehint.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var bool|null
     */
    public $enableMixedTypeHint = true;

    /**
     * Enforces to transform @var string|int into native string|int typehint.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var bool bool|null
     */
    public $enableUnionTypeHint = true;
}
