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
     * We use this var to reduce the hard dependencies on internals from a specific slevomat version and we get more
     * explicit and navigateable codes.
     *
     * @var Sniff|null
     */
    protected ?Sniff $testedObject = null;

    /**
     * Asserts that two variables have the same type and value.
     *
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @phpcsSuppress BestIt.TypeHints.ReturnTypeDeclaration.MissingReturnTypeHint
     *
     * @param mixed $expected The expected value.
     * @param mixed $actual The actual value.
     * @param string $message The optional error message.
     *
     * @return void
     */
    abstract public static function assertSame($expected, $actual, string $message = ''): void;

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
        static::assertSame($this->getExpectedTokens(), $this->testedObject->register());
    }
}
