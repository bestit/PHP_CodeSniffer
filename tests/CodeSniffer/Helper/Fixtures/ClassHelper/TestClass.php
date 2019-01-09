<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper\ClassHelper\Fixtures;

use BestIt\Sniffs\ClassRegistrationTrait;
use BestIt\Sniffs\FunctionRegistrationTrait;

class TestClass
{
    use ClassRegistrationTrait;
    use FunctionRegistrationTrait;
}