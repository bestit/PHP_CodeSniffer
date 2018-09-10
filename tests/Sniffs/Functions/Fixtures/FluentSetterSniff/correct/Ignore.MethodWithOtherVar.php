<?php

class FluentSetterSniff
{
    // Method does not match var name, so skip!
    private $tesT;

    public function setTest($test)
    {
        $this->tesT = $test;
    }
}
