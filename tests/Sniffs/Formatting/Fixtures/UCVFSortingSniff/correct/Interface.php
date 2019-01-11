<?php

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;

interface Complete {
    const FOO = 'BAR';

    public function foo(): string;
}