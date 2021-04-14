<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\Fixtures\UseSpacingSniff\correct;

use function uniqid;
use function var_dump;

class Correct
{
    public function __construct()
    {
        var_dump(uniqid());
    }
}