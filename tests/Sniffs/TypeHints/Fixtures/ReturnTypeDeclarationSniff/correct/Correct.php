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
     * @phpcsSuppress BestIt.TypeHints.ReturnTypeDeclaration.MissingReturnTypeHint
     * @return int
     */
    public function testIntMethod()
    {
        return 1;
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
    public function testVoidMethod(): void
    {
        //void
    }

    /**
     * @return null
     */
    public function testNullMethod(): ?string
    {
        return null;
    }

    /**
     * @return array|null
     */
    public function testMultipleTypesMethod(): ?array
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
