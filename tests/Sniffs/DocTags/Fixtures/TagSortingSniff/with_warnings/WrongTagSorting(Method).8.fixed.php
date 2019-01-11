<?php

class Multiples
{
    /**
     * This is a test method.
     *
     * @Route("/foo")
     * @Route("/bar")
     * @throws RuntimeException
     *
     * @param string $param1
     * @param string $param2
     *
     * @todo Test1
     * @todo Test2
     * @todo Test3
     *
     * @return void
     */
    public function test(string $param1, string $param2)
    {
        throw new RuntimeException('To be implemented');
    }
}