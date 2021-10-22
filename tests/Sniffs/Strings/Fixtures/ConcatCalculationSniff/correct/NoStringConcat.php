<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Strings\Fixtures\ConcatCalculationSniff\correct;

class NoStringConcat
{
    public function __construct()
    {
        $test = 'hello ';

        var_dump(compact('test') + [$test . ' world']);
    }
}