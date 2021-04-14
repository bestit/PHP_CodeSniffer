<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use SlevomatCodingStandard\Sniffs\Classes\EmptyLinesAroundClassBracesSniff;

/**
 * There MUST be no lines after the opening brace.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class NoWhitespaceAfterClassOpeningSniff extends EmptyLinesAroundClassBracesSniff
{
    /**
     * There MUST be no lines after the opening brace.
     *
     * @var int
     */
    public $linesCountAfterOpeningBrace = 0;

    /**
     * There MUST be no lines before the closing brace.
     *
     * @var int
     */
    public $linesCountBeforeClosingBrace = 0;
}
