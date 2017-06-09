<?php

class FluentSetterSniff
{
    private $test;

    public function setTest($test)
    {
        $this->test = $test;
    }
}
