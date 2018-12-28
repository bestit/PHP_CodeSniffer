<?php

class ForbiddenFunctionDieTest
{
    public function __construct()
    {
        die('foobar');
    }
}