<?php

namespace BestIt\Sniffs\NamingConventions;

class CorrectClass {
    public $testProp;

    private function testMethod()
    {
        $testVar = 'foo';
        $this->testProp = $testVar;

        return $this->testProp;
    }
}