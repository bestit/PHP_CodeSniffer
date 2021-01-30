<?php

class MissingNativeTypeHint
{
    /**
     * @var int|string
     */
    private int|string $intString;

    /**
     * @var int|string|array
     */
    private int|string|array $longMixed;

    /**
     * @var mixed
     */
    private mixed $mixed;

    /**
     * @var int
     */
    private int $number;

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var array<string>
     */
    public array $packages;

    /**
     * @var \PHP_CodeSniffer\Files\File
     */
    public \PHP_CodeSniffer\Files\File $file;
}
