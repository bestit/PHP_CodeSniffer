<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Checks the registered tokens.
 *
 * @author Bjoern Lange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait TestTokenRegistrationTrait
{
    /**
     * The tested class.
     *
     * We use this var to reduce the hard dependencies on internals from a specific slevomat version.
     *
     * @var Sniff|void
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
     * Returns the tokens which should be checked.
     *
     * @return array Returns the expected token ids.
     */
    abstract protected function getExpectedTokens(): array;

    /**
     * Checks if the correct tokens are registered.
     *
     * @return void
     */
    public function testRegisteredTokens(): void
    {
        static::assertSame($this->getExpectedTokens(), $this->fixture->register());
    }
}
