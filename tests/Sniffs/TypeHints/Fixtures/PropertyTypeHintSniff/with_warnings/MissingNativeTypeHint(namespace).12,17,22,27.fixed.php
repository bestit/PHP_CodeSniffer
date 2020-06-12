<?php

namespace BestIt\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;

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
     * @var array
     */
    public array $packages;

    /**
     * @var File
     */
    public File $file;
}
