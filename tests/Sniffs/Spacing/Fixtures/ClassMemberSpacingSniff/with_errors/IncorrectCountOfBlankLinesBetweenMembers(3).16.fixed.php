<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing\Fixtures\DifferentClassMemberSpacing\with_errors;

class IncorrectCountOfBlankLinesBetweenMembers
{
    protected $property1 = '';

    /**
     * IncorrectCountOfBlankLinesBetweenMembers constructor.
     */
    private function __construct()
    {
    }
}
