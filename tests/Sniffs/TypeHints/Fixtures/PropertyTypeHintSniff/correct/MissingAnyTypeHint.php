<?php

class MissingAnyTypeHint
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
     */
    private $number;

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
     */
    protected $name;

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
     */
    public $packages;

    public function __construct(
        int $number,
        string $name,
        array $packages
    )
    {
        $this->number = 5;
        $this->name = 'BestIt';
        $this->packages = [];
    }
}
