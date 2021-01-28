<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions\Fixtures\NoNamedArgumentsSniff\with_errors;

class DisallowedNamedArgumentMethod
{
    public function __construct()
    {
        $this->hello(text: 'World');
    }

    public function hello($text)
    {
        echo $text;
    }
}