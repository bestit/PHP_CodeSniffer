<?php

class Correct
{
    private $test;

    public function setTest($test)
    {
        $this->test = $test;

        if ($this->setFoo($test) && $this->setFoo($this->test)) {
            $this->setFoo($this->test = $test);
        }

        $this->setFoo()
            ->setFoo();

        $this->setFoo()
            ->setFoo()
            ->setFoo();

        $this
            ->setFoo()
            ->setFoo();

        return $this;
    }

    public function setFoo($test)
    {
        return $this;
    }
}
