<?php
declare(strict_types=1);

class OpenTagSniff
{
    private $test;

    public function setTest($test)
    {
        $this->test = $test;

        return $this;
    }
}
