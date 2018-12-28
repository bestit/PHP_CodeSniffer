<?php

class ForbiddenFunctionEvalTest
{
    public function __construct()
    {
        eval('foobar');
    }
}