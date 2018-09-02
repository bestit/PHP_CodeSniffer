<?php

namespace Test;

use InvalidArgumentException;

/**
 * Class Test
 *
 * @package Test
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class Test
{
    /**
     * Testing function.
     *
     * This is a long description for the testing function.
     * It goes ober multiple lines.
     *
     * @param string $test This is an explanation why this is a string.
     *                     This description is so long that it goes over multiple lines.
     * @param string $test2 Test 1,2,3
     *
     * @return bool This is an explanation why this returns a bool.
     *              Its so long that it goes over multiple lines.
     */
    public function testing($test, $test2)
    {
        return true;
    }

    /**
     * Testing function for void
     *
     * @return void
     */
    public function testing2()
    {
    }

    /**
     * Testing function for void
     *
     * @return void
     *
     * @throws InvalidArgumentException Because invalid
     */
    public function testing3()
    {
        throw new InvalidArgumentException('Meeeep');
    }
}
