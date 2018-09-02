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
     * Testing function.
     *
     * This is a long description for the testing function.
     * It goes over multiple lines.
     *
     * @param mixed $test Test 1,2,3
     *
     * @return mixed
     */
    public function testing($test)
    {
        if ($test) {
            return 'test';
        }

        return true;
    }
}
