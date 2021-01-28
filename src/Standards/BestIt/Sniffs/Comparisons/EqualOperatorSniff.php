<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons;

use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\SuppressingTrait;
use const T_IS_EQUAL;
use const T_IS_NOT_EQUAL;

/**
 * Class EqualOperatorSniff.
 *
 * @package BestIt\Sniffs\Comparisons
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 */
class EqualOperatorSniff extends AbstractSniff
{
    use SuppressingTrait;

    /**
     * You SHOULD use the "Identical" operator (===).
     */
    public const CODE_EQUAL_OPERATOR_FOUND = 'EqualOperatorFound';

    /**
     * Warning when an equal operator was found.
     *
     * @var string WARNING_EQUAL_OPERATOR_FOUND
     */
    private const MESSAGE_EQUAL_OPERATOR_FOUND = 'Please check if you could use the "Identical" operator (===).';

    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $file = $this->getFile();
        $stackPos = $this->getStackPos();

        $file->recordMetric($stackPos, 'Found not wanted T_IS_EQUAL', 'yes');

        if (!$this->isSniffSuppressed(static::CODE_EQUAL_OPERATOR_FOUND)) {
            $file->addError(
                self::MESSAGE_EQUAL_OPERATOR_FOUND,
                $stackPos,
                static::CODE_EQUAL_OPERATOR_FOUND,
            );
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
        return [T_IS_EQUAL, T_IS_NOT_EQUAL];
    }
}
