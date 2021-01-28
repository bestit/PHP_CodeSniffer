<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions\Fixtures\NoNamedArgumentsSniff\with_errors;

class DisallowedNamedArgumentFunction
{
    public function __construct()
    {
        sprintf(format: 'Hello %s', values: ['World']);
    }
}