<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Classes\Fixtures\ModernClassNameReferenceSniff\correct;

use function print_r;

class Correct
{
    public function __construct()
    {
        echo $this::class;
        var_dump(Correct::class);

        $var = $this;
        print_r($var::class);
    }
}