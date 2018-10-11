<?php

class SuccessfulParamTagTest {
    /**
     * Fubar.
     *
     * @param string $bar Bar!
     * @param string $baz baz!
     */
    public function foo(string $bar)
    {
        var_dump($bar);
    }
}