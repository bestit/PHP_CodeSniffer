<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing\Fixtures\ConstantSpacingSniff\with_errors;

class IncorrectCountOfBlankLinesAfterConstant
{
    const CONST_1 = 1;

    /**
     * dummy.
     */
    const CONST_2 = 2;
}