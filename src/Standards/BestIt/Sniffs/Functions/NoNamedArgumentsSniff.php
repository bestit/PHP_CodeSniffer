<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use SlevomatCodingStandard\Sniffs\Functions\DisallowNamedArgumentsSniff;

/**
 * There MUST be no named arguments.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class NoNamedArgumentsSniff extends DisallowNamedArgumentsSniff
{
}
