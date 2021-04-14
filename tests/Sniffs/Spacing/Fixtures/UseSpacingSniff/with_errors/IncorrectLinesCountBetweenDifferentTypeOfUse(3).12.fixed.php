<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\Fixtures\UseSpacingSniff\with_errors;

use SplObjectStorage;
use stdClass;
use function uniqid;
use function var_dump;

class IncorrectLinesCountBetweenDifferentTypeOfUse
{
    public function __construct()
    {
        $storage = new SplObjectStorage();
        $storage->attach(new stdClass(), uniqid());

        var_dump($storage);
    }
}