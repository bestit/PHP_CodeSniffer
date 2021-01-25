<?php

namespace BestIt\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;

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
     * @var array
     */
    public $packages;

    /**
     * @var File
     */
    public $file;
}
