<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\File;
use BestIt\CodeSniffer\Helper\ExceptionHelper;
use PHP_CodeSniffer\Files\File as BaseFile;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\SuppressHelper;

/**
 * Class AbstractSniff
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs
 */
abstract class AbstractSniff implements Sniff
{
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
     * The used suppresshelper.
     *
     * @var SuppressHelper
     */
    private $suppressHelper = null;

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
        return new ExceptionHelper($this->file);
    }

    /**
     * Get the sniff name.
     *
     * @param string|null $sniffName If there is an optional sniff name.
     *
     * @return string Returns the special sniff name in the code sniffer context.
     */
    private function getSniffName(?string $sniffName = null): string
    {
        $sniffClassName = preg_replace(
            '/Sniff$/',
            '',
            str_replace(['\\', '.Sniffs'], ['.', ''], static::class)
        );

        if ($sniffName) {
            $sniffClassName .= '.' . $sniffName;
        }

        return $sniffClassName;
    }

    /**
     * Returns the used suppress helper.
     *
     * @return SuppressHelper The suppress helper.
     */
    private function getSuppressHelper(): SuppressHelper
    {
        if (!$this->suppressHelper) {
            $this->suppressHelper = new SuppressHelper();
        }

        return $this->suppressHelper;
    }

    /**
     * Returns true if this sniff or a rule of this sniff is suppressed with the slevomat suppress annotation.
     *
     * @param null|string $rule The optional rule.
     *
     * @return bool Returns true if the sniff is suppressed.
     */
    protected function isSniffSuppressed(?string $rule = null): bool
    {
        return $this->getSuppressHelper()->isSniffSuppressed(
            $this->file,
            $this->stackPos,
            $this->getSniffName($rule)
        );
    }

    /**
     * Called when one of the token types that this sniff is listening for is found.
     *
     * @param BaseFile $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int $stackPos The position in the PHP_CodeSniffer file's token stack where the token was found.
     *
     * @return void
     */
    public function process(BaseFile $phpcsFile, $stackPos)
    {
        $this->file = new File($phpcsFile);
        $this->stackPos = $stackPos;
        $this->tokens = $this->file->getTokens();
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
