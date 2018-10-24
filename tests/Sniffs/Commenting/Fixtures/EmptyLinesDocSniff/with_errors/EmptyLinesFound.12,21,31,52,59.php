<?php

declare(strict_types=1);

/**
 * Class ManyEmptyDocLines.
 *
 *
 *
 *
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt
 */
class EmptyLinesDocSniff
{
    /**
     * This is a property.
     *
     *
     * @var $property int
     */
    private $property;

    /**
     * Test function.
     *
     *
     *
     *
     * @param $value A value.
     *
     *
     *
     *
     *
     *
     * @return int
     */
    private function test($value)
    {
        /** @var int $value */
        $value += 10;

        return $value;
    }

    /**
     * Test function.
     *
     *
     * @param $value
     *
     *
     *
     * @return int
     *
     *
     */
    private function test2($value)
    {
        $value += 100;

        return $value;
    }
}
