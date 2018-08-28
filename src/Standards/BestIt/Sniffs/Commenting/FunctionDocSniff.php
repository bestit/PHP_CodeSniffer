<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\FunctionRegistrationTrait;

/**
 * Checks the structure of the method summary.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class FunctionDocSniff extends AbstractDocSniff
{
    use FunctionRegistrationTrait;
}
