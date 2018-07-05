<?php

class NoSingleObjectOperator
{
    private $test;

    public function setTest($test)
    {
        $this->setFoo()->setFoo();

        $this->test->setFoo();

        $this->setFoo('string')->setFoo();

        $this->setFoo($this)->setFoo();

        $this->setFoo($bar = new NoSingleObjectOperator())->setFoo();

        $this
            ->setFoo()->setFoo();

        return $this;
    }

    public function setFoo($test)
    {
        return $this;
    }
}
