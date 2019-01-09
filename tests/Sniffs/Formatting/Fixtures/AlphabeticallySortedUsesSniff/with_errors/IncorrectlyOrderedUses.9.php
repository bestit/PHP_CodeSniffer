<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\AlphabeticallySortedUsesSniff\Fixtures;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use PHPUnit\Framework\TestCase;
use const T_OPEN_TAG;
use const T_ANON_CLASS;
use function typeHint;
use function array_map;

class WrongClass
{

}
