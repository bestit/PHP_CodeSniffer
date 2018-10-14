<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

/**
 * Helps you with the stack pos of the new api.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait StackPosTrait
{
    /**
     * Position of the listened token.
     *
     * @var int|void
     */
    protected $stackPos;

    /**
     * Type-safe getter for the stack position.
     *
     * @return int
     */
    protected function getStackPos(): int
    {
        return $this->stackPos;
    }
}
