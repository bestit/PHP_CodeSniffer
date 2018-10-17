<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\AbstractSniff;
use const T_IS_EQUAL;

/**
 * Class EqualOperatorSniff.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class EqualOperatorSniff extends AbstractSniff
{
    /**
     * You should use the "Identical" operator (===)
     *
     * @var string CODE_EQUAL_OPERATOR_FOUND
     */
    public const CODE_EQUAL_OPERATOR_FOUND = 'EqualOperatorFound';

    /**
     * Warning when an equal operator was found.
     *
     * @var string WARNING_EQUAL_OPERATOR_FOUND
     */
    private const MESSAGE_EQUAL_OPERATOR_FOUND = 'Please check if you could use the "Identical" operator (===).';

    /**
     * Is this sniff allowed to fix?
     *
     * @var bool $isFixing
     */
    public $isFixable = false;
    
    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $file = $this->getFile();
        $stackPos = $this->getStackPos();

        $isFixing = $file->addFixableWarning(
            self::MESSAGE_EQUAL_OPERATOR_FOUND,
            $stackPos,
            self::CODE_EQUAL_OPERATOR_FOUND
        );

        $file->recordMetric($stackPos, 'Found not wanted T_IS_EQUAL', 'yes');

        if ($isFixing && $this->isFixable) {
            $this->replaceToken();
        }
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
        $file = $this->getFile();

        $file->fixer->beginChangeset();
        $file->fixer->replaceToken($this->getStackPos(), '===');
        $file->fixer->endChangeset();
    }
}
