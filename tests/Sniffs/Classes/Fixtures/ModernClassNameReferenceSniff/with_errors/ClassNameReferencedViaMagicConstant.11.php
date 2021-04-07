<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Classes\Fixtures\ModernClassNameReferenceSniff\with_errors;

class ClassNameReferencedViaMagicConstant
{
    public function __construct()
    {
        echo __CLASS__;
    }
}