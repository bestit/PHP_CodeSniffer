<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\FunctionRegistrationTrait;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use function in_array;
use function str_repeat;
use const T_FUNCTION;
use const T_STRING;

/**
 * Sniffs the required tags of a constant.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class RequiredMethodTagsSniff extends AbstractRequiredTagsSniff
{
    use FunctionRegistrationTrait;

    /**
     * Adds a return annotation if there is none.
     *
     * @param int $stackPos
     *
     * @return void
     */
    protected function fixMinReturn(int $stackPos): void
    {
        $closePos = $this->tokens[$stackPos]['comment_closer'];
        $closeTag = $this->tokens[$closePos];
        $indent = str_repeat(' ', $closeTag['column'] - 1);
        $file = $this->getFile();

        $returnTypeHint = FunctionHelper::findReturnTypeHint(
            $file,
            $file->findNext([T_FUNCTION], $closePos + 1),
        );

        $typeHint = $returnTypeHint ? $returnTypeHint->getTypeHint() : 'void';

        $fixer = $file->fixer;

        $fixer->beginChangeset();

        $fixer->replaceToken(
            $closePos,
            "*\n{$indent}* @return {$typeHint}\n{$indent}{$closeTag['content']}",
        );

        $fixer->endChangeset();
    }

    /**
     * Returns the minimum count for the return tag.
     *
     * @return int
     */
    protected function getReturnMinimumCount(): int
    {
        return $this->isMagicFunctionWithoutReturn() ? 0 : 1;
    }

    /**
     * Returns the required tag data.
     *
     * The order in which they appear in this array os the order for tags needed.
     *
     * @return array List of tag metadata
     */
    protected function getTagRules(): array
    {
        return [
            'return' => [
                'min' => [$this, 'getReturnMinimumCount'],
                'max' => 1,
            ],
        ];
    }

    /**
     * Checks if the listener function is a magic php function without return.
     *
     * @return bool Indicator if the current function is not a whitelisted function
     */
    private function isMagicFunctionWithoutReturn(): bool
    {
        $whitelist = [
            '__construct',
            '__destruct',
            '__clone',
            '__wakeup',
            '__set',
            '__unset',
        ];

        $stackToken = $this->tokens[$this->stackPos];

        $functionNamePtr = $this->file->findNext(
            [T_STRING],
            $this->stackPos + 1,
            $stackToken['parenthesis_opener'],
        );

        $functionNameToken = $this->tokens[$functionNamePtr];

        return in_array($functionNameToken['content'], $whitelist, true);
    }
}
