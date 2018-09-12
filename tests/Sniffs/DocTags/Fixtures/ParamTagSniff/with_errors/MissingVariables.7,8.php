<?php

class SuccessfulParamTagTest {
    /**
     * Fubar.
     *
     * @param string $baar Bar!
     * @param string $baz baz!
     */
    public function foo()
    {
        var_dump($bar, $baz);
    }
}