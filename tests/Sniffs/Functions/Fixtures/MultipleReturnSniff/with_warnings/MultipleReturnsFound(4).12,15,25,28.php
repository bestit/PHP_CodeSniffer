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

    function test2($value)
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