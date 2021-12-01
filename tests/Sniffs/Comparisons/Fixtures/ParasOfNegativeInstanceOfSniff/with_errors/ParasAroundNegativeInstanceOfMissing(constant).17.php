<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons\Fixtures\ParasOfNegativeInstanceOfSniff\with_errors;

use BestIt\SniffTestCase;
use Carbon\CarbonPeriod;
use DomainException;
use function uniqid;
use const T_STRING;

class ParasAroundNegativeInstanceOfMissing
{
    public function __construct()
    {
        if (!T_STRING instanceof SniffTestCase) {
            throw new DomainException('The view should contain the time period.');
        }
    }
}