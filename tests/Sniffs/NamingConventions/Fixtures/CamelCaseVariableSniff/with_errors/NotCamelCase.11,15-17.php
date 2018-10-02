<?php

namespace BestIt\Sniffs\NamingConventions;

class SuppressedClass {
    /**
     * A tet var.
     *
     * @var string
     */
    public $test_pro = 'foo';

    private function testMethod()
    {
        $test_var = $this->test_pro;
        $TestVar2 = $test_var;
        $TESTVAR3 = $TestVar2;
        $validVar = $TESTVAR3;

        return $validVar;
    }
}