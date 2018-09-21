<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\ClassRegistrationTrait;

/**
 * Class ClassDocSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class ClassDocSniff extends AbstractDocSniff
{
    use ClassRegistrationTrait;
}
