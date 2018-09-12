<?php

class SuccessfulParamTagTest {
    /**
     * Fubar.
     *
     * @param string $baar Bar!
     * @param string $baz baz!
     */
    public function foo(string $bar, string $baz)
    {
        var_dump($bar, $baz);
    }
}