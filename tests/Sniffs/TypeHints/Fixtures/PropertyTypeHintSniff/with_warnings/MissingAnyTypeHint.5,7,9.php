<?php

class MissingAnyTypeHint
{
    private $number;

    protected $name;

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
