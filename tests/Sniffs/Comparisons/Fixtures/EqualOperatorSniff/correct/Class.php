<?php

declare(strict_types=1);

/**
 * Class Correct.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
class Correct
{
    /**
     * Test function.
     *
     * @param $a
     * @param $b
     *
     * @return string
     */
    function test($a, $b): string
    {
        if ($a === $b) {
            return 'Success';
        }

        /** @phpcsSuppress BestIt.Comparisons.EqualOperator.EqualOperatorFound */
        if ($team != 'best it') {
            return 'WHAT?!';
        }
    }
}
