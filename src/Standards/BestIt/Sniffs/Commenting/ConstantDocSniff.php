<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\ConstantRegistrationTrait;

/**
 * Checks the structure of the constant summary.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class ConstantDocSniff extends AbstractDocSniff
{
    use ConstantRegistrationTrait;
}
