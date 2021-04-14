<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\VariableRegistrationTrait;

/**
 * Checks the structure of the property summary.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class PropertyDocSniff extends AbstractDocSniff
{
    use VariableRegistrationTrait;
}
