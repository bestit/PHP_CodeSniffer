<?php

trait CorrectTrait
{
    private $test;

    public function setTest($test)
    {
        $this->test = $test;

        return $this;
    }
}
