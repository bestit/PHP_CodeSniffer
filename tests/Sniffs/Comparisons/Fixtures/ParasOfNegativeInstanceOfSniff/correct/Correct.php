<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons\Fixtures\ParasOfNegativeInstanceOfSniff\correct;

use BestIt\SniffTestCase;

class Correct
{
    public function __construct()
    {
        var_dump(($this instanceof Correct) && (!($this instanceof SniffTestCase)));
    }
}