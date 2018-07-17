<?php

class GenericArray
{
    /**
     * foo
     *
     * @return bool
     */
    public function testBoolMethod()
    {
        return false;
    }

    /**
     * @param int $foo
     * @param array $bar
     *
     * @return int
     */
    public function testIntMethod($foo, $bar)
    {
        return 1;
    }

    /** @return array|null */
    public function testMultipleTypesMethod()
    {
        if (true) {
            return [];
        }

        return null;
    }
}
