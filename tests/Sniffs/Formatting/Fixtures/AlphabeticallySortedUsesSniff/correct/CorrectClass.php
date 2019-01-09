<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\AlphabeticallySortedUsesSniff\Fixtures;

use BestIt\SniffTestCase;
use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\TestRequiredConstantsTrait;
use PHPUnit\Framework\TestCase;
use function array_map;
use function typeHint;
use const T_ANON_CLASS;
use const T_OPEN_TAG;

class CorrectClass
{

}
