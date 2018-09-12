<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

/**
 * Helps you with the registration of the constant sniffs.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait ConstantRegistrationTrait
{
    /**
     * Sniffs for constants.
     *
     * @return array
     */
    public function register(): array
    {
        return [T_CONST];
    }
}
