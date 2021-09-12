<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Strings\Fixtures\ConcatCalculationSniff\with_errors;

class Errors
{
    public function __construct()
    {
        $b = 10;
        echo $b + 2 .
            'This string is korrect + ' . 1 + $b . ' und enthält mehrere Concats: ' .
            $b
                -
                1
            ;
    }
}