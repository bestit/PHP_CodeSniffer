<?php

class ClassWithErrorMixedType {
    /**
     * Fubar.
     *
     * @param string|int $bar Bar!
     * @param string $baz Baz!
     */
    public function foo($bar, string $baz)
    {
        var_dump($bar, $baz);
    }
}