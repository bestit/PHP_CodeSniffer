<?php

class ClassWithErrorMixedType {
    /**
     * Fubar.
     *
     * @param string|int $bar Bar!
     * @param string $baz Baz!
     * @param array $bars Bars!
     */
    public function foo($bar, string $baz, string ...$bars)
    {
        var_dump($bar, $baz);
    }
}