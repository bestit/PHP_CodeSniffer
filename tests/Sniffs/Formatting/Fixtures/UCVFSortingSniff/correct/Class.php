<?php

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;

class Complete {
    use DefaultSniffIntegrationTestTrait;

    const FOO = 'BAR';

    const BAR = 'BAZ';

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