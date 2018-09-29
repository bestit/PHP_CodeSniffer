<?php

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;

interface Complete {
    public const FOO = 'BAR';

    public function foo(): string;
}