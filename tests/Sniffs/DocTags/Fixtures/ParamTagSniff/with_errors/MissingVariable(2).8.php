<?php

class SuccessfulParamTagTest {
    /**
     * Fubar.
     *
     * @param string $bar Bar!
     * @param string $baaz baz!
     */
    public function foo(string $bar, string $baz)
    {
        var_dump($bar, $baz);
    }
}