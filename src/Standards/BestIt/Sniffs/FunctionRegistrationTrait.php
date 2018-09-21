<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use const T_FUNCTION;

/**
 * Helps you with the registration of the method tests.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait FunctionRegistrationTrait
{
    /**
     * Sniffs for constants.
     *
     * @api
     *
     * @return array
     */
    public function register(): array
    {
        return [T_FUNCTION];
    }
}
