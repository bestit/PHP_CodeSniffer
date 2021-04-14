<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\Fixtures\NoWhitespaceAfterClassOpening\with_errors;

class IncorrectEmptyLinesAfterOpeningBrace
{

    public function __construct()
    {
        echo 'Hello World';
    }
}