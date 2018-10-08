<?php

use BestIt\CodeSniffer\File;

class TypeHintDeclarationSniff
{
    /**
     * TypeHintDeclarationSniff constructor.
     */
    public function __construct()
    {
        // Do nothing.
    }

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

    /**
     * @return null|File[]
     */
    public function testTypesArrayMethod()
    {
        return [];
    }

    /**
     * @return void
     */
    public function testVoidMethod()
    {
        //void
    }

    /**
     * @return null
     */
    public function testNullMethod()
    {
        return null;
    }

    /**
     * @return array|null
     */
    public function testMultipleTypesMethod()
    {
        if (true) {
            return [];
        }

        return null;
    }
}
