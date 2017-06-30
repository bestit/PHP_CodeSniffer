<?php

class FluentSetterSniff
{
    private $test;

    public function setupDatabase()
    {
    }

    public function setTest($test)
    {
        $this->test = $test;

        return $this;
    }
}
