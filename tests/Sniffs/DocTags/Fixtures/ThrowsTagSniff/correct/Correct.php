<?php

class WithThrow {
    /**
     * @throws Exception to test.
     */
    public function foo()
    {
        throw new Exception();
    }
}