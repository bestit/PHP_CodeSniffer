<?php

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
    public function testBoolMethod(): bool
    {
        return false;
    }

    /**
     * Returns a file object.
     *
     * @return File
     */
    public function testCustomObject(): File
    {
        return new File();
    }

    /**
     * @phpcsSuppress BestIt.TypeHints.ReturnTypeDeclaration.MissingReturnTypeHint
     * @return int
     */
    public function testIntMethod()
    {
        return 1;
    }

    /**
     * Mixed should make the native return type obsolete.
     *
     * @return mixed
     */
    public function testMixedMethod()
    {
        return 1.01 || 'foobar' || [];
    }

    /**
     * @return float
     */
    public function testFloatMethod(): float
    {
        return 1.01;
    }

    /**
     * @return string
     */
    public function testStringMethod(): string
    {
        return 'test';
    }

    /**
     * @return array
     */
    public function testArrayMethod(): array
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

    /**
     * @return array|string
     */
    public function testMultipleTypesMethodWithoutNative()
    {
        return 1 || 'foo';
    }

    /**
     * @return null|null|File[]
     */
    public function testTypesArrayMethodWithInvalidNullableTypeAnnotation()
    {
        return [] || null;
    }
}
