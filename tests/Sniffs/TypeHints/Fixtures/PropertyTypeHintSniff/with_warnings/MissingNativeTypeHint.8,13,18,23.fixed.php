<?php

class MissingNativeTypeHint
{
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
