<?php

declare(strict_types = 1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\TypeHints\DeclareStrictTypesSniff;
use PHPUnit\Framework\TestCase;

/**
 * Test for DeclareStrictTypesSniff.
 *
 * @package BestIt\Sniffs\Functions
 * @author Tim Kellner <tim.kellner@bestit-online.de>
 */
class DeclareStrictTypesSniffTest extends TestCase
{
    /**
     * Test DeclareStrictTypesSniff constructor.
     *
     * @return void
     */
    public function testConstructor()
    {
        $fixture = new DeclareStrictTypesSniff();

        self::assertSame(2, $fixture->newlinesCountBetweenOpenTagAndDeclare);
        self::assertSame(1, $fixture->spacesCountAroundEqualsSign);
    }
}
