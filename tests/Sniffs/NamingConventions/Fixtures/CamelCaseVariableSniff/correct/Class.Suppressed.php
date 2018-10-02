<?php

namespace BestIt\Sniffs\NamingConventions;

class SuppressedClass {
    /**
     * A tet var.
     *
     * @phpcsSuppress BestIt.NamingConventions.CamelCaseVariable.NotCamelCase
     * @var string
     */
    public $test_pro = 'foo';

    private function testMethod()
    {
        /**
         * @phpcsSuppress BestIt.NamingConventions.CamelCaseVariable.NotCamelCase
         * @var string $test_var
         */
        $test_var = 'foo';

        return $test_var;
    }
}