<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing\Fixtures\ConstantSpacingSniff\with_errors;

class IncorrectCountOfBlankLinesAfterConstant
{
    protected $property1 = 1;

    /**
     * dummy.
     */
    protected $property2 = 2;
}