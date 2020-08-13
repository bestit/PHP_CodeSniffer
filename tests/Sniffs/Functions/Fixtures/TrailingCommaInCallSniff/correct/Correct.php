<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions\Fixtures\TrailingCommaInCallSniff\correct;

use function var_dump;

class Correct
{
    public function __construct()
    {
        var_dump(
            'foo',
            'bar',
            'baz',
        );
    }
}