<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer;

use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Fixer;
use function func_get_args;

/**
 * Class File
 *
 * Wrapper Class for PhpCsFile to provide a consistent way to replace int|bool returns
 * with int returns (false => -1)
 * Additionally there could be some architecture changes in the future, like Token-Objects and so on.
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\CodeSniffer
 */
class File extends AbstractFileDecorator
{
    /**
     * Returns the position of the next specified token(s).
     *
     * If a value is specified, the next token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns false if no token can be found.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @param array $types The type(s) of tokens to search for.
     * @param int $start The position to start searching from in the token stack.
     * @param int|null $end The end position to fail if no token is found. if not specified or null, end will default to the end of the token stack.
     * @param bool $exclude If true, find the next token that is NOT of a type specified in $types.
     * @param string|null $value The value that the token(s) must be equal to. If value is omitted, tokens with any value will be returned.
     * @param bool $local If true, tokens outside the current statement will not be checked. i.e., checking will stop at the next semi-colon found.
     *
     * @return int Returns the pointer of the token or -1
     */
    public function findNext(
        $types,
        $start,
        $end = null,
        $exclude = false,
        $value = null,
        $local = false
    ): int {
        $result = $this->__call(__FUNCTION__, func_get_args());

        return $this->harmonizeFindResult($result);
    }

    /**
     * Returns the position of the previous specified token(s).
     *
     * If a value is specified, the previous token of the specified type(s)
     * containing the specified value will be returned.
     *
     * Returns -1 if no token can be found.
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     *
     * @param array $types The type(s) of tokens to search for.
     * @param int $start The position to start searching from in the token stack.
     * @param int|null $end The end position to fail if no token is found. if not specified or null, end will default to the start of the token stack.
     * @param bool $exclude If true, find the previous token that are NOT of the types specified in $types.
     * @param string|null $value The value that the token(s) must be equal to. If value is omitted, tokens with any value will be returned.
     * @param bool $local If true, tokens outside the current statement will not be checked. IE. checking will stop at the previous semi-colon found.
     *
     * @return int Pointer to the found token
     */
    public function findPrevious(
        $types,
        $start,
        $end = null,
        $exclude = false,
        $value = null,
        $local = false
    ): int {
        $pointer = $this->__call(__FUNCTION__, func_get_args());

        return $this->harmonizeFindResult($pointer);
    }

    /**
     * Returns the eol char of the file
     *
     * @return string Returns the EndOfLine-Character of the processed file
     */
    public function getEolChar(): string
    {
        return $this->getBaseFile()->eolChar;
    }

    /**
     * Returns the Wrapped PHP_CodeSniffer_Fixer
     *
     * @return Fixer Returns the fixer class.
     */
    public function getFixer(): Fixer
    {
        return $this->getBaseFile()->fixer;
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
     * Returns an integer even if the search in the code sniffer file returns a bool.
     *
     * @param int|bool $searchResult Pointer of a token or false.
     *
     * @return int The real Pointer or -1 when not found.
     */
    public function harmonizeFindResult($searchResult): int
    {
        return $searchResult !== false ? $searchResult : -1;
    }
}
