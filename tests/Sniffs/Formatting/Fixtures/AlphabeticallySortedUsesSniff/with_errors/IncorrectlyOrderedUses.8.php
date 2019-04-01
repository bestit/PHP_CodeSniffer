<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\AlphabeticallySortedUsesSniff\Fixtures;

use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\TestRequiredConstantsTrait;
use BestIt\SniffTestCase;
use PHPUnit\Framework\TestCase;
use Exception;
use stdClass;
use const T_OPEN_TAG;
use const T_ANON_CLASS;
use function typeHint;
use function array_map;

class WrongClass
{

}
