<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions\Fixtures\TrailingCommaInCallSniff\with_errors;

use function var_dump;

class MissingTrailingComma
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