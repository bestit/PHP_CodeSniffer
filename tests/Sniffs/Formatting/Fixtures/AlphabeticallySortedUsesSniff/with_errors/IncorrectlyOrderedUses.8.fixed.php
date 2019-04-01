<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\AlphabeticallySortedUsesSniff\Fixtures;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use function array_map;
use function typeHint;
use const T_ANON_CLASS;
use const T_OPEN_TAG;

class WrongClass
{

}
