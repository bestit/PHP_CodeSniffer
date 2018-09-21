<?php

class Test
{
    public function foo(string $bar = 'baz', ?string $baz = null): string
    {
        return $bar;
    }
}
