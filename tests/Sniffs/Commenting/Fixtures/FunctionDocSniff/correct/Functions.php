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
     * This is the summary.
     *
     * This is the long description.
     * 2. And its second line.
     *
     * @return void
     */
    public function testing($test, $test2)
    {
        return true;
    }

    /**
     * Testing function for void.
     */
    public function testing2()
    {
    }

    /**
     * 3. Testing function for void
     *
     * @return void
     */
    public function testing3()
    {
        throw new InvalidArgumentException('Meeeep');
    }

    /**
     * 4. Testing function for void
     *
     * LongDescription:
     *
     * - foo
     * - bar
     * - baz
     *
     * @return void
     */
    public function testing4()
    {
        throw new InvalidArgumentException('Meeeep');
    }
}
