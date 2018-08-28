<?php

class TypeHintDeclarationSniff
{
    /**
     * @return bool
     */
    public function testBoolMethod()
    {
        return false;
    }

    /**
     * @return int
     */
    public function testIntMethod()
    {
        return 1;
    }

    /**
     * @return float
     */
    public function testFloatMethod()
    {
        return 1.01;
    }

    /**
     * @return string
     */
    public function testStringMethod()
    {
        return 'test';
    }

    /**
     * @return array
     */
    public function testArrayMethod()
    {
        return [];
    }
}
