<?php

namespace Test;

/**
 * Class Test
 *
 * @package Test
 * @author Stephan Weber <stephan.weber@bestit-online.de>
 */
class Test
{
    /**
     * Test
     *
     * @param string $test
     * @param string $test2
     *
     * @return bool
     */
    public function testing($test, $test2)
    {
        return true;
    }

    /**
     * Test2
     *
     * @param string $test
     * @param string $test2
     * @param string $test3
     *
     * @return bool
     */
    public function testing2($test, $test2, $test3)
    {
        return true;
    }

    /**
     * Test3
     *
     * @param string $test
     * @param string $test2
     * @param string $test3
     *
     * @return bool
     *
     * @throws Exception1
     * @throws Exception2
     * @throws Exception3
     */
    public function testing3($test, $test2, $test3)
    {
        return true;
    }
}
