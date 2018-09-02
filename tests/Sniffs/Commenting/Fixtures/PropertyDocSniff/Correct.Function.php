<?php

namespace Test;

/**
 * Class Test
 *
 * @package Test
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class Test
{
    /**
     * Test property.
     *
     * With long description.
     * Over multiple lines.
     *
     * @var string
     */
    public $test = 'Im a test property';

    /**
     * Test function
     *
     * @return void
     */
    public function test()
    {
        /** @var int $test */
        $test = 1;
    }
}
