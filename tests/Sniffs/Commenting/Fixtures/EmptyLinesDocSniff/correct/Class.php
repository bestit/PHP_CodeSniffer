<?php

/**
 * Class EmptyLinesDocSniff.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
class EmptyLinesDocSniff
{
    /**
     * This is a property.
     *
     * @see Test.
     * @var $property Property.
     */
    private $property;

    /**
     * Correct.
     *
     * @return void
     */
    private function correct(): void
    {
        /** @var int $something One line comment. */
        $something = 1;
    }
}
