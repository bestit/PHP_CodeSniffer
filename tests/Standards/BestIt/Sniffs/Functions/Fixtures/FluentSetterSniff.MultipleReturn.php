<?php

class FluentSetterSniff
{
    private $test;

    public function setTest($test)
    {
        $this->test = $test;

        if ($test === 1) {
            return false;
        }

        return $this;
    }
}
