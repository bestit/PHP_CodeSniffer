<?php

class SecondMissingDesc {
    /**
     * Fubar.
     *
     * @param string $bar Bar!
     * @param string $baz
     */
    public function foo(string $bar, string $baz)
    {
        var_dump($bar, $baz);
    }
}