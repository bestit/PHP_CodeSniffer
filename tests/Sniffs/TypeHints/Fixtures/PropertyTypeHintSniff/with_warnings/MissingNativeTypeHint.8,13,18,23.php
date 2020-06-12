<?php

class MissingNativeTypeHint
{
    /**
     * @var int
     */
    private $number;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var array<string>
     */
    public $packages;

    /**
     * @var \PHP_CodeSniffer\Files\File
     */
    public $file;
}
