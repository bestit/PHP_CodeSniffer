<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Classes\Fixtures\ModernClassNameReferenceSniff\with_errors;

class ClassNameReferencedViaFunctionCall
{
    public function __construct()
    {
        echo static::class;
    }
}