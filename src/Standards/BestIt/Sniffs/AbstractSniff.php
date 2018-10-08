<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\File;
use BestIt\CodeSniffer\Helper\ExceptionHelper;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Class AbstractSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs
 */
abstract class AbstractSniff implements Sniff
{
    use SuppressingTrait;

    /**
     * The used file.
     *
     * @var File|void
     */
    protected $file;

    /**
     * Position of the listened token.
     *
     * @var int|void
     */
    protected $stackPos;

    /**
     * The used token.
     *
     * @var array|void
     */
    protected $token;

    /**
     * All tokens of the class.
     *
     * @var array|void The tokens of the class.
     */
    protected $tokens;

    /**
     * Returns true if the requirements for this sniff are met.
     *
     * @return bool Are the requirements met and the sniff should proceed?
     */
    protected function areRequirementsMet(): bool
    {
        return true;
    }

    /**
     * Default method for fixing exceptions.
     *
     * @param CodeWarning $exception
     *
     * @return void
     */
    protected function fixDefaultProblem(CodeWarning $exception): void
    {
        // Satisfy PHP MD
        unset($exception);
    }

    /**
     * Returns an exception handler for the sniffed file.
     *
     * @return ExceptionHelper Returns the exception helper.
     */
    protected function getExceptionHandler(): ExceptionHelper
    {
        return new ExceptionHelper($this->getFile());
    }

    /**
     * Type-safe getter for the file.
     *
     * @return File
     */
    protected function getFile(): File
    {
        return $this->file;
    }

    /**
     * Type-safe getter for the stack position.
     *
     * @return int
     */
    protected function getStackPos(): int
    {
        return $this->stackPos;
    }

    /**
     * Called when one of the token types that this sniff is listening for is found.
     *
     * @param BaseFile $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int $stackPos The position in the PHP_CodeSniffer file's token stack where the token was found.
     *
     * @return void
     */
    public function process(BaseFile $phpcsFile, $stackPos): void
    {
        $this->file = new File($phpcsFile);
        $this->stackPos = $stackPos;
        $this->tokens = $this->getFile()->getTokens();
        $this->token = $this->tokens[$stackPos];

        $this->setUp();

        if ($this->areRequirementsMet()) {
            try {
                $this->processToken();
            } catch (CodeWarning | CodeError $exception) {
                $withFix = $this->getExceptionHandler()->handleException($exception);

                if ($withFix) {
                    $this->fixDefaultProblem($exception);
                }
            }
        }

        $this->tearDown();
    }

    /**
     * Processes the token.
     *
     * @return void
     */
    abstract protected function processToken(): void;

    /**
     * Do you want to setup things before processing the token?
     *
     * @return void
     */
    protected function setUp(): void
    {
    }

    /**
     * Is there something to destroy after processing the token?
     *
     * @return void
     */
    protected function tearDown(): void
    {
    }
}
