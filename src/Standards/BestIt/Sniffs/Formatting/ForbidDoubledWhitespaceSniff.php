<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use SlevomatCodingStandard\Sniffs\Whitespaces\DuplicateSpacesSniff;

/**
 * You MUST not duplicate whitespace to indent vars.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class ForbidDoubledWhitespaceSniff extends DuplicateSpacesSniff
{
}
