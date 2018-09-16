<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

/**
 * Class PropertyDocSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class PropertyDocSniff extends ConstantDocSniff
{
    /**
     * Returns which tokens should be listened to.
     *
     * @return int[] List of tokens which is to be listened to
     */
    public function getListenedTokens(): array
    {
        return [
            T_VARIABLE
        ];
    }
}
