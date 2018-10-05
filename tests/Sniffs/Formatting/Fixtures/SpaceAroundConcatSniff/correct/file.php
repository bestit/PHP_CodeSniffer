<?php

class CorrectStrings {
    protected $foo = 'This is a valid string.And this dots should be ignored.';

    protected $bar = 'This is a valid concatinated string' . 'And this dots should be ignored.';

    protected $baz = 'This is a valid concatinated string' .
        'And this dots should be ignored.';

    /**
     * This dot here.should absolutely be ignored.
     */
    public function test()
    {
        return $this->foo . $this->bar . $this->baz . 'Finish.';
    }
}