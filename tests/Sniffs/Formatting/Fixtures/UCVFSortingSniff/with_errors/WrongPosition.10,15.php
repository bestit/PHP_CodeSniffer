<?php

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;

class Complete {
    use DefaultSniffIntegrationTestTrait;

    const FOO = 'BAR';

    public function foo(): string
    {
        return 'bar';
    }

    public $foo = 'bar';
}