<?php

class TypeHintDeclarationSniff
{
    public function testMethodWithoutDoc()
    {
        return false;
    }

    /**
     * Returns a file object.
     *
     * @return File
     */
    public function testCustomObject()
    {
        return new File();
    }
}
