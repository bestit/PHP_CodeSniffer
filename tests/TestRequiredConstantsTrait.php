<?php

declare(strict_types=1);

namespace BestIt;

use function constant;

/**
 * Helps you checking required constants in a class.
 *
 * @package BestIt
 * @author Bjoern Lange <bjoern.lange@bestit-online.de>
 */
trait TestRequiredConstantsTrait
{
    /**
     * The tested class.
     *
     * We use this var to reduce the hard dependencies on internals from a specific slevomat version.
     *
     * @var mixed
     */
    protected $fixture;

    /**
     * Asserts that two variables have the same type and value.
     *
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @param mixed $expected The expected value.
     * @param mixed $actual The actual value.
     * @param string $message The optional error message.
     *
     * @return void
     */
    abstract public static function assertSame($expected, $actual, $message = '');

    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    abstract public function getRequiredConstantAsserts(): array;

    /**
     * Checks if the api is extended.
     *
     * @param string $constant The name of the constant.
     * @param string $constantValue We check the value as well, because some constants are fixed in ruleset.xmls.
     *
     * @return void
     *
     * @dataProvider getRequiredConstantAsserts
     */
    public function testRequiredConstants(string $constant, ?string $constantValue = null)
    {
        $fullConstantName = get_class($this->fixture) . '::' . $constant;

        static::assertTrue(
            defined($fullConstantName),
            'Constant ' . $fullConstantName . ' is missing.'
        );

        $fixture = $this->fixture;

        if ($constantValue !== null) {
            static::assertSame(
                constant($fullConstantName),
                $constantValue,
                'The value of the constants ' . $fullConstantName . ' was wrong.'
            );
        }
    }
}