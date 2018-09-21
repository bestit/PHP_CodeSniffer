<?php

class SuccessfulParamTagTest {
    /**
     * Fubar.
     *
     * @param string $bar Bar!
     * @param $baz baz!
     */
    public function foo(string $bar, string $baz)
    {
        var_dump($bar, $baz);
    }
}