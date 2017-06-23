<?php

class MethodDocSniff
{
    /**
     * short method summary here
     *
     * @return void
     */
    public function setupDatabase()
    {
    }

    /**
     * short method summary here
     *
     * long summary here
     *
     * @param $test
     * @return $this
     */
    public function setTest($test)
    {
        $this->test = $test;

        return $this;
    }
}
