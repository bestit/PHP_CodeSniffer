<?php

class Test
{
    public function foo(string $bar = 'baz', $baz = null): string
    {
        return $bar;
    }
}
