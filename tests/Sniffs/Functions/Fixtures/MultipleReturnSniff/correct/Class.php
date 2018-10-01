<?php

class CorrectClass
{
    function test(): string
    {
        $foo = 'test';

        return $foo;
    }

    public function withAnonClass()
    {
        return new class() {
            function foo(): string {
                return 'bar';
            }
        };
    }

    public function withClosure(): Closure
    {
        return function(): string {
            return 'foo';
        };
    }
}