<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\ClassRegistrationTrait;

/**
 * Checks the structure of the class summary.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class ClassDocSniff extends AbstractDocSniff
{
    use ClassRegistrationTrait;
}
