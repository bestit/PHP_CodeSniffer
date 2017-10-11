<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer;

use PHP_CodeSniffer_File;

/**
 * Class File
 *
 * Wrapper Class for PHP_CodeSniffer_File to provide a consistent way to replace int|bool returns
 * with int returns (false => -1)
 * Additionally there could be some architecture changes in the future, like Token-Objects and so on.
 *
 * @package BestIt\CodeSniffer
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class File
{
    /**
     * The CodeSniffer file
     *
     * @var PHP_CodeSniffer_File
     */
    private $baseFile;

    /**
     * Wrapped PHP_CodeSniffer_Fixer
     *
     * @var Fixer
     */
    private $fixer;

    /**
     * Contains the advanced token stack
     *
     * @var array
     */
    private $tokens;

    /**
     * File constructor.
     *
     * @param PHP_CodeSniffer_File $baseFile CodeSniffer file
     */
    public function __construct(PHP_CodeSniffer_File $baseFile)
    {
        $this->baseFile = $baseFile;
        $this->fixer = new Fixer($this, $this->baseFile->fixer);
        $this->tokens = $this->getAdvancedTokens();
    }

    /**
     * Adds the pointer to all token data arrays.
     *
     * @return array Advanced token stack.
     */
    private function getAdvancedTokens(): array
    {
        $tokens = [];

        foreach ($this->baseFile->getTokens() as $tokenPtr => $token) {
            $token['pointer'] = $tokenPtr;
            $tokens[$tokenPtr] = $token;
        }

        return $tokens;
    }

    /**
     * Records a fixable error against a specific token in the file.
     *
     * @param string $error The error message.
     * @param int $stackPtr The stack position where the error occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the error message.
     * @param int $severity The severity level for this error.
     *                      A value of 0 will be converted into the default severity level.
     *
     * @return bool Returns true if the error was recorded and should be fixed.
     */
    public function addFixableError(
        string $error,
        int $stackPtr,
        string $code = '',
        array $data = [],
        int $severity = 0
    ): bool {
         return $this->baseFile->addFixableError($error, $stackPtr, $code, $data, $severity);
    }

    /**
     * Returns the token stack for this file.
     *
     * @return array Return array of token data
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * Returns the position of the previous specified token(s).
     *
     * If a value is specified, the previous token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns -1 if no token can be found.
     *
     * @param array $types The type(s) of tokens to search for.
     * @param int $start The position to start searching from in the token stack.
     * @param int|null $end The end position to fail if no token is found.
     *        if not specified or null, end will default to the start of the token stack.
     * @param bool $exclude If true, find the previous token that are NOT of the types specified in $types.
     * @param string|null $value The value that the token(s) must be equal to.
     *        If value is omitted, tokens with any value will be returned.
     * @param bool $local If true, tokens outside the current statement will not be checked.
     *        IE. checking will stop at the previous semi-colon found.
     *
     * @return int Pointer to the found token
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function findPrevious(
        array $types,
        int $start,
        $end = null,
        bool $exclude = false,
        $value = null,
        bool $local = false
    ): int {
        $pointer = $this->baseFile->findPrevious($types, $start, $end, $exclude, $value, $local);

        return $this->preparePointer($pointer);
    }

    /**
     * Records an error against a specific token in the file.
     *
     * @param string $error The error message.
     * @param int $stackPtr The stack position where the error occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the error message.
     * @param int $severity The severity level for this error. A value of 0
     *                      will be converted into the default severity level.
     * @param bool $fixable Can the error be fixed by the sniff?
     *
     * @return bool Returns true if setting the error was done or false
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addError(
        string $error,
        int $stackPtr,
        string $code = '',
        array $data = [],
        int $severity = 0,
        bool $fixable = false
    ): bool {
        return $this->baseFile->addError($error, $stackPtr, $code, $data, $severity, $fixable);
    }

    /**
     * Records an error against a specific line in the file.
     *
     * @param string $error The error message.
     * @param int $line The line on which the error occurred.
     * @param string $code A violation code unique to the sniff message.
     * @param array $data Replacements for the error message.
     * @param int $severity The severity level for this error. A value of 0
     *                      will be converted into the default severity level.
     *
     * @return bool Returns true of the error got recorded
     */
    public function addErrorOnLine(
        string $error,
        int $line,
        string $code = '',
        array $data = [],
        int $severity = 0
    ): bool {
        return $this->baseFile->addErrorOnLine($error, $line, $code, $data, $severity);
    }

    /**
     * Returns the position of the next specified token(s).
     *
     * If a value is specified, the next token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @param array $types The type(s) of tokens to search for.
     * @param int $start The position to start searching from in the
     *                   token stack.
     * @param int|null $end The end position to fail if no token is found. if not specified or null, end will default to
     *                 the end of the token stack.
     * @param bool $exclude If true, find the next token that is NOT of a type specified in $types.
     * @param string|null $value The value that the token(s) must be equal to.
     *                      If value is omitted, tokens with any value will be returned.
     * @param bool $local If true, tokens outside the current statement will not be checked. i.e., checking will stop
     *                    at the next semi-colon found.
     *
     * @return int Returns the pointer of the token or -1
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function findNext(
        array $types,
        int $start,
        $end = null,
        bool $exclude = false,
        $value = null,
        bool $local = false
    ): int {
        $result = $this->baseFile->findNext($types, $start, $end, $exclude, $value, $local);

        return $this->preparePointer($result);
    }

    /**
     * Prepares given pointer result.
     *
     * @param int|bool $pointer Pointer of a token
     *
     * @return int Pointer or -1 when not found
     */
    public function preparePointer($pointer): int
    {
        return $pointer !== false ? $pointer : -1;
    }

    /**
     * Returns the Wrapped PHP_CodeSniffer_Fixer
     *
     * @return Fixer Returns the wrapped PHP_CodeSniffer_Fixer
     */
    public function getFixer(): Fixer
    {
        return $this->fixer;
    }

    /**
     * Returns the eol char of the file
     *
     * @return string Returns the EndOfLine-Character of the processed file
     */
    public function getEolChar(): string
    {
        return $this->baseFile->eolChar;
    }
}
