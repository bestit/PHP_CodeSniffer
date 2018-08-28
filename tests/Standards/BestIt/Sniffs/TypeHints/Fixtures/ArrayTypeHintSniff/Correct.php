<?php

class Correct
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
     * @param string[] $bar
     *
     * @return int
     */
    public function testIntMethod($foo, $bar)
    {
        return 1;
    }

    /** @return int[]|null */
    public function testMultipleTypesMethod()
    {
        if (true) {
            return [];
        }

        return null;
    }

    /**
     * @param string[] $foo Expect an array
     * @param string[] Array expected
     *
     * @return int
     */
    public function testBar($foo, $bar)
    {
        return 1;
    }
}
