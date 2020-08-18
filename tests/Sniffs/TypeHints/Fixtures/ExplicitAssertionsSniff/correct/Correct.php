<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints\Fixtures\ExplicitAssertionsSniffTest\correct;

class Correct
{
    public function __construct()
    {
        assert($this instanceof Correct);
    }
}