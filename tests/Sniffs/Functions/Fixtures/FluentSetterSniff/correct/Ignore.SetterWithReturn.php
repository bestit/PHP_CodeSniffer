<?php

class FluentSetterSniff
{
    private $test;

    public function setTest($test): bool
    {
        $this->test = $test;

        return true;
    }
}
