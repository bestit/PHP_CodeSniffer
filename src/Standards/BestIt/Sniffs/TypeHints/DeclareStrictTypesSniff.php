<?php

declare(strict_types = 1);

namespace BestIt\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff as BaseSniff;

/**
 * Sniff which extend slevomat sniff to prevent problems with autoloading.
 *
 * @package BestIt\Sniffs\TypeHints
 * @author Tim Kellner <tim.kellner@bestit-online.de>
 */
class DeclareStrictTypesSniff extends BaseSniff
{
    /**
     * DeclareStrictTypesSniff constructor which override default settings.
     */
    public function __construct()
    {
        $this->newlinesCountBetweenOpenTagAndDeclare = 2;
        $this->spacesCountAroundEqualsSign = 1;
    }
}
