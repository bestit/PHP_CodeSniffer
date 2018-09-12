<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\FunctionRegistrationTrait;

/**
 * Class MethodDocSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class MethodDocSniff extends AbstractDocSniff
{
    use FunctionRegistrationTrait;
}
