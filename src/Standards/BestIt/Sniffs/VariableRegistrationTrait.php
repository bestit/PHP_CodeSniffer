<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use const T_VARIABLE;

/**
 * Helps you with the registration of the var sniffs.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait VariableRegistrationTrait
{
    /**
     * Sniffs for variables.
     *
     * @return array
     */
    public function register(): array
    {
        return [
            T_VARIABLE
        ];
    }
}
