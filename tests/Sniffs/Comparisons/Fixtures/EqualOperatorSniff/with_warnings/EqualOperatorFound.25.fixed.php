<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\Fixtures\EqualOperatorSniff\with_warnings;

/**
 * Class EqualOperatorFound.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\Formatting\Fixtures\EqualOperatorSniff\with_warnings
 */
class EqualOperatorFound
{
    /**
     * Test function.
     *
     * @param $a
     * @param $b
     *
     * @return string
     */
    function foo($a, $b): string
    {
        if ($a === $b) {
            return 'Error';
        }
    }
}
