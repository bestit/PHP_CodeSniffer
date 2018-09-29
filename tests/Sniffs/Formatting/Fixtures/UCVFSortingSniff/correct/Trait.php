<?php

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;

trait Complete {
    use DefaultSniffIntegrationTestTrait;

    public $foo = 'bar';

    public function foo(): string
    {
        return 'bar';
    }
}