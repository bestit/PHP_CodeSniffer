<?php

class MultipleReturnsFoundSniff
{
    function test($value)
    {
        if ($value === 1) {
            return true;
        }

        if ($value > 1) {
            return false;
        }

        return null;
    }
}