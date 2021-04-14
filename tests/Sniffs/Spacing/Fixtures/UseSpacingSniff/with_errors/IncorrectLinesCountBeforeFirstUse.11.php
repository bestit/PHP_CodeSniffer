<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\Fixtures\UseSpacingSniff\with_errors;





use function uniqid;
use function var_dump;

class IncorrectLinesCountBeforeFirstUse
{
    public function __construct()
    {
        var_dump(uniqid());
    }
}