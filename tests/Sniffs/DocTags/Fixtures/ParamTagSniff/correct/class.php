<?php

class SuccessfulParamTagTest
{
    /**
     * Fubar.
     *
     * @ignoredAnnotation
     *
     * @param string $bar Bar!
     * @param string $baz baz!
     */
    public function foo(string $bar, string $baz)
    {
        var_dump($bar, $baz);
    }
}