<?php

class ClassWithErrorMixedType {
    /**
     * Fubar.
     *
     * @param mixed $bar
     * @param string $baz Baz!
     * @param mixed $callbacks Possible callbacks.
     */
    public function foo($bar, string $baz, ...$callbacks)
    {
        var_dump($bar, $baz);
    }
}