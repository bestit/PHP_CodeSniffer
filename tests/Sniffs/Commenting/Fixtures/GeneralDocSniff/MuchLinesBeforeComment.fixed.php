<?php

/**
 * Class GeneralDoc
 */
class GeneralDoc
{
    /**
     * Test var.
     *
     * @var int
     */
    private $test;

    /**
     * Testing for GeneralDocSniff.
     *
     * @param array $param
     * @param $isActive
     *
     * @return void
     */
    public function testing(array $param, $isActive)
    {
        /**
         * Test variable
         *
         * @var int
         */
        $test = 1;
    }

    /**
     * Testing for GeneralDocSniff.
     *
     * @param array $param
     * @param $isActive
     *
     * @return void
     */
    public function testing2(array $param, $isActive)
    {
    }
}
