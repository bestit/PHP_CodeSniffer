<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Comparisons;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\Sniffs\AbstractSniff;
use function array_key_exists;
use const T_ARRAY;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSE_SHORT_ARRAY;
use const T_IS_EQUAL;
use const T_IS_IDENTICAL;
use const T_IS_NOT_EQUAL;
use const T_IS_NOT_IDENTICAL;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;

/**
 * Prevents that you compare against an empty array.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Comparisons
 */
class EmptyArrayForComparisonSniff extends AbstractSniff
{
    /**
     * You MUST not create an empty array, to check for an empty array.
     */
    public const CODE_EMPTY_ARRAY = 'EmptyArray';

    /**
     * Used message to display the error.
     */
    private const MESSAGE_EMPTY_ARRAY = 'Please do not initalize an empty array to check for an empty array!';

    /**
     * The check must not contain just an empty array.
     *
     * @var array|null Is filled by the setup method.
     */
    private ?array $invalidStructure = null;

    /**
     * Search starting with the given search pos for the invalid codes in consecutive order.
     *
     * @throws CodeError Contains the error message if there is an invalid array check.
     *
     * @param array $invalidCodes
     * @param int $searchPos
     *
     * @return void
     */
    private function checkArrayStructure(array $invalidCodes, int $searchPos): void
    {
        // Rename the var to get more readable code.
        $remainingInvalidCodes = $invalidCodes;
        unset($invalidCodes);

        foreach ($remainingInvalidCodes as $nextInvalidCodeIndex => $nextInvalidCode) {
            $foundTokenPos = TokenHelper::findNextEffective($this->getFile(), $searchPos);
            $foundToken = $this->tokens[$foundTokenPos];

            // We can stop the search, if there is no invalid code.
            if ($foundToken['code'] !== $nextInvalidCode) {
                break;
            }

            // Check the next possible token
            $searchPos = $foundTokenPos + 1;
            unset($remainingInvalidCodes[$nextInvalidCodeIndex]);
        }

        $matchedEveryInvalidCode = !$remainingInvalidCodes;

        $this->file->recordMetric(
            $searchPos,
            'Invalid array comparison',
            $matchedEveryInvalidCode ? 'yes' : 'no'
        );

        if ($matchedEveryInvalidCode) {
            throw (new CodeError(static::CODE_EMPTY_ARRAY, self::MESSAGE_EMPTY_ARRAY, $searchPos));
        }
    }

    /**
     * Processes the token.
     *
     * @throws CodeError Contains the error message if there is an invalid array check.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this->file->recordMetric($this->getStackPos(), 'Used comparison (for array checks)', $this->token['type']);

        $nextTokenPos = TokenHelper::findNextEffective($this->getFile(), $startPos = $this->getStackPos() + 1);
        $nextToken = $this->tokens[$nextTokenPos];

        // Add in Array check
        if (array_key_exists($nextToken['code'], $this->invalidStructure)) {
            $this->checkArrayStructure($this->invalidStructure[$nextToken['code']], $nextTokenPos + 1);
        } else {
            $this->file->recordMetric(
                $startPos,
                'Invalid array comparison',
                'no'
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
     * @see Tokens.php
     *
     * @return int[]
     */
    public function register(): array
    {
        return [T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL];
    }

    /**
     * Declares the forbidden array structure.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->invalidStructure = [
            T_ARRAY => [T_OPEN_PARENTHESIS, T_CLOSE_PARENTHESIS],
            T_OPEN_SHORT_ARRAY => [T_CLOSE_SHORT_ARRAY]
        ];
    }
}
