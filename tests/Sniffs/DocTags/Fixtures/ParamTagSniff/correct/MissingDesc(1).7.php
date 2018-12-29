<?php

class SecondMissingDesc {
    /**
     * Fubar.
     *
     * @param string $bar
     * @param string $baz Baz!
     */
    public function foo(string $bar, string $baz)
    {
        var_dump($bar, $baz);
    }
}