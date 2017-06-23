<?php

class MethodDocSniff
{
    /**
     * Short method summary here
     * @return void
     */
    public function setupDatabase()
    {
    }

    /**
     * Short method summary here
     * @param $test
     * @return $this
     */
    public function setTest($test)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Short method summary here
     * BAZ
     */
    public function setTestTwo($test)
    {
        $this->test = $test;

        return $this;
    }
}
