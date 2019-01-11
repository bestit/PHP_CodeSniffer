<?php

class Multiples
{
    /**
     * This is a test method.
     *
     * @return void
     * @Route("/foo")
     * @Route("/bar")
     * @param string $param1
     * @param string $param2
     * @throws RuntimeException
     * @todo Test1
     * @todo Test2
     * @todo Test3
     */
    public function test(string $param1, string $param2)
    {
        throw new RuntimeException('To be implemented');
    }
}