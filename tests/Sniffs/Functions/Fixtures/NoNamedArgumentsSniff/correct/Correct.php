<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions\Fixtures\NoNamedArgumentsSniff\correct;

use function sprintf;

class Correct
{
    public function __construct()
    {
        sprintf('Hello %s', 'world');
    }
}