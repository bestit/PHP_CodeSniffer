<?php

class MethodDocSniff
{
    /**
     * Foo
     *
     * @return string
     */
    public function setupDatabase()
    {
        /** @var string $test */
        $test = 'FOO';

        return $test;
    }
}
