<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\AbstractSniff;

/**
 * Class EqualOperatorSniff.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class EqualOperatorSniff extends AbstractSniff
{
    /**
     * Is this sniff allowed to fix?
     *
     * @var bool $isFixing
     */
    public $isFixable = false;

    /**
     * Code when an equal operator was found.
     *
     * @var string CODE_EQUAL_OPERATOR_FOUND
     */
    public const CODE_EQUAL_OPERATOR_FOUND = 'EqualOperatorFound';

    /**
     * Warning when an equal operator was found.
     *
     * @var string WARNING_EQUAL_OPERATOR_FOUND
     */
    private const WARNING_EQUAL_OPERATOR_FOUND = 'Please check if you could use the "Identical" operator (===).';

    /**
     * Find the T_IS_EQUAL token and add a warning.
     *
     * @return void
     */
    private function findToken(): void
    {
        if ($this->token['code'] === T_IS_EQUAL) {
            $isFixing = $this->file->addFixableWarning(
                self::WARNING_EQUAL_OPERATOR_FOUND,
                $this->stackPos,
                self::CODE_EQUAL_OPERATOR_FOUND
            );

            if ($isFixing && $this->isFixable) {
                $this->replaceToken($this->file, $this->stackPos);
            }
        }
    }

    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this->findToken();
    }

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * An example return value for a sniff that wants to listen for whitespace
     * and any comments would be:
     *
     * <code>
     *    return array(
     *            T_WHITESPACE,
     *            T_DOC_COMMENT,
     *            T_COMMENT,
     *           );
     * </code>
     *
     * @see    Tokens.php
     *
     * @return int[]
     */
    public function register(): array
    {
        return [T_IS_EQUAL];
    }

    /**
     * Replace token with the T_IS_IDENTIAL token.
     *
     * @return void
     */
    private function replaceToken(): void
    {
        $this->file->fixer->beginChangeset();
        $this->file->fixer->replaceToken($this->stackPos, '===');
        $this->file->fixer->endChangeset();
    }
}
