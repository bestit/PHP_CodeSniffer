<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Strings\Fixtures\ConcatCalculationSniff\correct;

class Correct
{
    public function __construct()
    {
        $b = 10;
        echo 'This string is korrect + ' . (1 + $b) . ' und enthält mehrere Concats: ' . ($b -1);
    }
}