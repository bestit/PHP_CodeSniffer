<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\Fixtures\NoWhitespaceAfterClassOpening\correct;

class Correct
{
    public function __construct()
    {
        echo 'Hello World';
    }
}