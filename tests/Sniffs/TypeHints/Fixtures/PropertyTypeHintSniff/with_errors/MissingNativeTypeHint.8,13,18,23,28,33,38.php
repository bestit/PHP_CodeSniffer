<?php

class MissingNativeTypeHint
{
    /**
     * @var int|string
     */
    private $intString;

    /**
     * @var int|string|array
     */
    private $longMixed;

    /**
     * @var mixed
     */
    private $mixed;

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
