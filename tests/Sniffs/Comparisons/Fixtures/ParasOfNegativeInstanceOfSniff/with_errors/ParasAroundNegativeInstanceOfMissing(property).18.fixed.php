<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons\Fixtures\ParasOfNegativeInstanceOfSniff\with_errors;

use BestIt\SniffTestCase;
use Carbon\CarbonPeriod;
use DomainException;
use function uniqid;

class ParasAroundNegativeInstanceOfMissing
{
    public function __construct()
    {
        $this->test = uniqid();

        if (!($this->test instanceof SniffTestCase)) {
            throw new DomainException('The view should contain the time period.');
        }
    }
}