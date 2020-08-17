<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\CodeSniffer\Helper\LineHelper;
use BestIt\Sniffs\AbstractSniff;
use PHP_CodeSniffer\Files\File;

/**
 * Class EmptyLinesDocSniff.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class EmptyLinesDocSniff extends AbstractSniff
{
    /**
     * There MUST be no redundant lines in your doc block.
     *
     * @var string
     */
    public const CODE_EMPTY_LINES_FOUND = 'EmptyLinesFound';

    /**
     * Error message when empty line is detected.
     *
     * @var string
     */
    private const ERROR_EMPTY_LINES_FOUND = 'There are too many empty lines in your doc-block!';

    /**
     * Process token within scope.
     *
     * @return void
     */
    protected function processToken(): void
    {
        $this->searchEmptyLines($this->file, $this->stackPos);
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
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /**
     * Remove unnecessary lines from doc block.
     *
     * @param File $phpcsFile
     * @param array $nextToken
     * @param array $currentToken
     *
     * @return void
     */
    private function removeUnnecessaryLines(File $phpcsFile, array $nextToken, array $currentToken): void
    {
        $movement = 2;

        if ($nextToken['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            $movement = 1;
        }

        $phpcsFile->fixer->beginChangeset();

        (new LineHelper($this->file))->removeLines(
            $currentToken['line'] + $movement,
            $nextToken['line'] - 1,
        );

        $phpcsFile->fixer->endChangeset();
    }

    /**
     * Process method for tokens within scope and also outside scope.
     *
     * @param File $phpcsFile The sniffed file.
     * @param int $searchPosition
     *
     * @return void
     */
    private function searchEmptyLines(File $phpcsFile, int $searchPosition): void
    {
        $endOfDoc = $phpcsFile->findEndOfStatement($searchPosition);

        do {
            $currentToken = $phpcsFile->getTokens()[$searchPosition];
            $nextTokenPosition = (int) $phpcsFile->findNext(
                [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR],
                $searchPosition + 1,
                $endOfDoc,
                true,
            );

            if ($hasToken = ($nextTokenPosition > 0)) {
                $nextToken = $phpcsFile->getTokens()[$nextTokenPosition];
                $hasTooManyLines = ($nextToken['line'] - $currentToken['line']) > 2;

                if ($hasTooManyLines) {
                    $isFixing = $phpcsFile->addFixableError(
                        self::ERROR_EMPTY_LINES_FOUND,
                        $nextTokenPosition,
                        static::CODE_EMPTY_LINES_FOUND,
                    );

                    if ($isFixing) {
                        $this->removeUnnecessaryLines($phpcsFile, $nextToken, $currentToken);
                    }
                }

                $phpcsFile->recordMetric(
                    $searchPosition,
                    'DocBlock has too many lines',
                    $hasTooManyLines ? 'yes' : 'no',
                );

                $searchPosition = $nextTokenPosition;
            }
        } while ($hasToken);
    }
}
