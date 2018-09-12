<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\VariableRegistrationTrait;

/**
 * Class PropertyDocSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class PropertyDocSniff extends ConstantDocSniff
{
    use VariableRegistrationTrait;
}
