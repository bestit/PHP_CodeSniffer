<?php

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;

class Complete {
    use DefaultSniffIntegrationTestTrait;

    public const FOO = 'BAR';

    public const BAR = 'BAZ';

    public $foo = 'bar';

    public function foo(): string
    {
        $bar = 'baz';

        $returnFunction = function() use ($bar): string {
            return $bar;
        };

        return $returnFunction();
    }

    public function baz(): string
    {
        return 'bar';
    }
}