<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons\Fixtures\ParasOfNegativeInstanceOfSniff\with_errors;

use BestIt\SniffTestCase;

class ParasAroundNegativeInstanceOfMissing
{
    public function __construct()
    {
        var_dump(($this instanceof Correct) && (!$this instanceof SniffTestCase));
    }
}