<?php

class ClassWithErrorMixedType {
    /**
     * Fubar.
     *
     * @param mixed $bar
     * @param string $baz Baz!
     */
    public function foo($bar, string $baz)
    {
        var_dump($bar, $baz);
    }
}