<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\TraitUseSpacingSniff\Fixtures;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\TestRequiredConstantsTrait;

class OneLineBetweenClass
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    public function __construct()
    {

    }
}