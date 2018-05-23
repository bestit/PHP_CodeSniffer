<?php

declare(strict_types = 1);

namespace BestIt\CodeSniffer;

use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Class AbstractSniff
 *
 * @package BestIt\Sniffs
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
abstract class AbstractSniff implements Sniff
{
    /**
     * The CodeSniffer file.
     *
     * @var BaseFile
     */
    private $baseFile;

    /**
     * A deferred class of PHP_CodeSniffer_File
     *
     * @var File
     */
    private $file;

    /**
     * Stack of all tokens found in the file
     *
     * @var array
     */
    private $tokens;

    /**
     * Pointer of the listened token.
     *
     * @var int
     */
    private $listenerPtr;

    /**
     * Token data of listened token
     *
     * @var array
     */
    private $listenerToken;

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[] Returns an array of tokens
     */
    public function register(): array
    {
        return $this->getRegisteredTokens();
    }

    /**
     * Called when one of the token types that this sniff is listening for is found.
     *
     * @param BaseFile $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int $stackPtr The position in the PHP_CodeSniffer file's token stack where the token was found.
     *
     * @return void
     */
    public function process(BaseFile $phpcsFile, $stackPtr)
    {
        $this->baseFile = $phpcsFile;
        $this->file = new File($phpcsFile);
        $this->tokens = $phpcsFile->getTokens();
        $this->listenerPtr = $stackPtr;
        $this->listenerToken = $this->tokens[$stackPtr];

        $this->processToken();
    }

    /**
     * Getter for deferred PHP_CodeSniffer class.
     *
     * @return File The deferred CodeSniffer file
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * Getter for listener pointer.
     *
     * @return int Returns pointer of the listened token
     */
    public function getListenerPointer(): int
    {
        return $this->listenerPtr;
    }

    /**
     * Getter for listener token data.
     *
     * @return array Returns token data of the listened token
     */
    public function getListenerToken(): array
    {
        return $this->listenerToken;
    }

    /**
     * Returns an array of registered tokens.
     *
     * @return int[] Returns array of tokens to listen for
     */
    abstract public function getRegisteredTokens(): array;

    /**
     * Processes a found registered token.
     *
     * @return void
     */
    abstract public function processToken();
}
