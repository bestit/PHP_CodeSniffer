<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions\Fixtures\NoNamedArgumentsSniff\with_errors;

class DisallowedNamedArgumentClosure
{
    public function __construct()
    {
        $call = fn ($text) => 'hello' . $text;

        echo $call(text: 'World');
    }
}