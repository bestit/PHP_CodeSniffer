<?php

class FluentSetterSniff
{
    private $test;

    /**
     * Test setter.
     *
     * @param string $test A test var.
     *
     * @return void
     *
     * @phpcsSuppress BestIt.Functions.FluentSetter.NoReturnFound
     */
    public function setTest(string $test)
    {
        $this->test = $test;
    }
}
