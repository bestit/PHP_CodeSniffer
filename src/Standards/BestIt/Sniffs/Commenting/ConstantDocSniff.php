<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\ConstantRegistrationTrait;

/**
 * Class ConstantDocSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class ConstantDocSniff extends AbstractDocSniff
{
    use ConstantRegistrationTrait;
}
