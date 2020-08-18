<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints\Fixtures\ExplicitAssertionsSniffTest\correct;

use BestIt\Sniffs\TypeHints\Fixtures\ExplicitAssertionsSniffTest\with_errors\RequiredExplicitAssertion;

class RequiredExplicitAssertion
{
    public function __construct()
    {
        $that = clone $this;
        assert($that instanceof RequiredExplicitAssertion);
    }
}