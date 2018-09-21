<?php

/**
 * Class to demonstrate a correct sorting.
 * All constants, properties and functions are correctly sorted by visibility and alphabetical.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
class CorrectSorting
{
    CONST BAR = 'bar';
    CONST FOO = 'foo';

    private $a;
    private $b;
    private $lorem;

    protected $c;
    protected $d;
    protected $ipsum;

    public $aa;
    public $bb;

    public function __construct()
    {
        $this->a = 'A';
        $this->b = 'B';
    }

    public function __destruct()
    {
    }

    private function foo()
    {
        return $this->a.' '.self::FOO;
    }

    protected function bar()
    {
        return $this->b.' '.self::BAR;
    }

    public function getFooBar()
    {
        $foo = $this->foo();
        return self::FOO.' '.self::BAR;
    }
}
