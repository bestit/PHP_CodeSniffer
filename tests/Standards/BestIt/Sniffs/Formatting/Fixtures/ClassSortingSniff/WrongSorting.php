<?php

/**
 * Class to demonstrate a wrong sorting.
 * All constants, properties and functions are shuffle sorted. This is very confusing.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
class WrongSorting
{
    CONST FOO = 'foo';
    protected $ipsum;

    private $a;

    private $b;
    protected $c;

    protected $d;
    public $aa;

    CONST BAR = 'bar';

    public $bb;
    private function foo()
    {
        return self::FOO;
    }

    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    protected function bar()
    {
        return self::BAR;
    }

    private $lorem;

    public function getFooBar()
    {
        return self::FOO.' '.self::BAR;
    }
}
