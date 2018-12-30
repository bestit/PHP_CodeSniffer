<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons;

use function array_key_exists;
use function in_array;

class CorrectArrayComparisons
{
    public function __construct()
    {
        $filled = ['foo' => 'bar'];

        if ($filled) {
            echo 'Yeah';
        }

        if (!empty($filled)) {
            echo 'Und Alle so Yeah!';
        }

        if ((array_key_exists('foo', $filled)) || (in_array('bar', $filled, true))) {
            echo 'Much better!';
        }

        if ($filled === ['foo' => 'bar']) {
            echo 'Yep, still OK.';
        }
    }
}
