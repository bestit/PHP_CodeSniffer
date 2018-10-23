<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Functions;

use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\FunctionRegistrationTrait;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_filter;
use function array_shift;
use function array_walk;
use const T_CLOSURE;
use const T_FUNCTION;
use const T_RETURN;

/**
 * Class MultipleReturnSniff.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\Functions
 */
class MultipleReturnSniff extends AbstractSniff
{
    use FunctionRegistrationTrait;

    /**
     * Code for multiple returns.
     */
    public const CODE_MULTIPLE_RETURNS_FOUND = 'MultipleReturnsFound';

    /**
     * Error message for multiple returns.
     */
    private const WARNING_MULTIPLE_RETURNS_FOUND = 'Multiple returns detected. Did you refactor your method? Please ' .
        'do not use an early return if your method/function still is cluttered.';

    /**
     * Only work on full fledged functions.
     *
     * @return bool True if there is a scope closer for this token.
     */
    protected function areRequirementsMet(): bool
    {
        return (bool) @ $this->token['scope_closer'];
    }

    /**
     * Returns the returns of this function.
     *
     * We check the "token level" to exclude the returns of nested closures.
     *
     * @return int[] The positions of the returns from the same function-scope.
     */
    private function loadReturnsOfThisFunction(): array
    {
        $returnPositions = TokenHelper::findNextAll(
            $this->file->getBaseFile(),
            [T_RETURN],
            $this->stackPos + 1,
            $this->token['scope_closer']
        );

        return array_filter($returnPositions, function (int $returnPos): bool {
            $possibleClosure = $this->file->findPrevious([T_CLOSURE, T_FUNCTION], $returnPos - 1, $this->stackPos);

            return $possibleClosure === $this->stackPos;
        });
    }

    /**
     * Iterates through the returns of this function and registers warnings if there is more then one relevant return.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $returnPositions = $this->loadReturnsOfThisFunction();

        if (count($returnPositions) > 1) {
            array_shift($returnPositions);

            array_walk($returnPositions, function (int $returnPos): void {
                $this->file->addWarning(
                    self::WARNING_MULTIPLE_RETURNS_FOUND,
                    $returnPos,
                    self::CODE_MULTIPLE_RETURNS_FOUND
                );
            });
        }
    }
}
