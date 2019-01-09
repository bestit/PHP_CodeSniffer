<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\CodeSniffer\Helper\UseStatementHelper;
use BestIt\Sniffs\AbstractSniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use function end;
use function reset;
use function strcmp;
use function uasort;
use const T_OPEN_TAG;
use const T_SEMICOLON;

/**
 * Checks if the use statements are sorted alphabetically by PSR-12 Standard.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class AlphabeticallySortedUsesSniff extends AbstractSniff
{
    /**
     * You MUST provide your imports in alphabetically order, PSR-12 compatible.
     */
    public const CODE_INCORRECT_ORDER = 'IncorrectlyOrderedUses';

    /**
     * The found use statements.
     *
     * @var UseStatement[]
     */
    private $useStatements;

    /**
     * Returns true if we have use statements.
     *
     * @return bool
     */
    protected function areRequirementsMet(): bool
    {
        return (bool) $this->useStatements = UseStatementHelper::getUseStatements(
            $this->getFile()->getBaseFile(),
            $this->getStackPos()
        );
    }

    /**
     * Will removing a compare marker for the given use statements.
     *
     * @param UseStatement $leftStatement
     * @param UseStatement $rightStatement
     *
     * @return int 1 <=> -1 To move statements in a direction.
     */
    private function compareUseStatements(UseStatement $leftStatement, UseStatement $rightStatement): int
    {
        $callbacks = [
            'compareUseStatementsByType',
            'compareUseStatementsByContent',
            // This will return something in any case!
            'compareUseStatementsByNamespaceCount'
        ];

        foreach ($callbacks as $callback) {
            $compared = $this->$callback($leftStatement, $rightStatement);

            if ($compared !== null) {
                return $compared;
            }
        }
    }

    /**
     * Compares the given use statements by their string content.
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param UseStatement $leftStatement
     * @param UseStatement $rightStatement
     *
     * @return int|null 1 <=> -1 To move statements in a direction.
     */
    private function compareUseStatementsByContent(UseStatement $leftStatement, UseStatement $rightStatement): ?int
    {
        $compareByContent = null;

        $aNameParts = explode(NamespaceHelper::NAMESPACE_SEPARATOR, $leftStatement->getFullyQualifiedTypeName());
        $bNameParts = explode(NamespaceHelper::NAMESPACE_SEPARATOR, $rightStatement->getFullyQualifiedTypeName());

        $minPartsCount = min(count($aNameParts), count($bNameParts));

        for ($i = 0; $i < $minPartsCount; ++$i) {
            $comparison = strcmp($aNameParts[$i], $bNameParts[$i]);

            if ($comparison) {
                $compareByContent = $comparison;
                break;
            }
        }

        return $compareByContent;
    }

    /**
     * The shorted usage comes at top.
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param UseStatement $leftStatement
     * @param UseStatement $rightStatement
     *
     * @return int 1 <=> -1 To move statements in a direction.
     */
    private function compareUseStatementsByNamespaceCount(
        UseStatement $leftStatement,
        UseStatement $rightStatement
    ): int {
        $aNameParts = explode(NamespaceHelper::NAMESPACE_SEPARATOR, $leftStatement->getFullyQualifiedTypeName());
        $bNameParts = explode(NamespaceHelper::NAMESPACE_SEPARATOR, $rightStatement->getFullyQualifiedTypeName());

        return count($aNameParts) <=> count($bNameParts);
    }

    /**
     * Classes to the top, functions next, constants last.
     *
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param UseStatement $leftStatement
     * @param UseStatement $rightStatement
     *
     * @return int|null 1 <=> -1 To move statements in a direction.
     */
    private function compareUseStatementsByType(UseStatement $leftStatement, UseStatement $rightStatement): ?int
    {
        $comparedByType = null;

        if (!$leftStatement->hasSameType($rightStatement)) {
            $order = [
                UseStatement::TYPE_DEFAULT => 1,
                UseStatement::TYPE_FUNCTION => 2,
                UseStatement::TYPE_CONSTANT => 3,
            ];

            $file = $this->getFile();
            $comparedByType = $order[UseStatementHelper::getType($file, $leftStatement)] <=>
                $order[UseStatementHelper::getType($file, $rightStatement)];
        }

        return $comparedByType;
    }

    /**
     * Sorts the uses correctly and saves them in the file.
     *
     * @param CodeWarning $error
     *
     * @return void
     */
    protected function fixDefaultProblem(CodeWarning $error): void
    {
        // Satisfy phpmd
        unset($error);

        $firstUseStatement = reset($this->useStatements);

        $file = $this->getFile()->getBaseFile();

        $file->fixer->beginChangeset();

        $this->removeOldUseStatements($firstUseStatement);

        $file->fixer->addContent(
            $firstUseStatement->getPointer(),
            $this->getNewUseStatements()
        );

        $file->fixer->endChangeset();
    }

    /**
     * Returns the new statements as a string for fixing.
     *
     * @return string
     */
    private function getNewUseStatements(): string
    {
        $this->sortUseStatements();

        $file = $this->getFile()->getBaseFile();

        return implode(
            $file->eolChar,
            array_map(
                function (UseStatement $useStatement) use ($file): string {
                    $unqualifiedName = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName(
                        $useStatement->getFullyQualifiedTypeName()
                    );

                    $useTypeName = UseStatementHelper::getTypeName($file, $useStatement);
                    $useTypeFormatted = $useTypeName ? sprintf('%s ', $useTypeName) : '';

                    return ($unqualifiedName === $useStatement->getNameAsReferencedInFile())
                        ? sprintf(
                            'use %s%s;',
                            $useTypeFormatted,
                            $useStatement->getFullyQualifiedTypeName()
                        )
                        : sprintf(
                            'use %s%s as %s;',
                            $useTypeFormatted,
                            $useStatement->getFullyQualifiedTypeName(),
                            $useStatement->getNameAsReferencedInFile()
                        );
                },
                $this->useStatements
            )
        );
    }

    /**
     * Checks if the uses are in the correct order.
     *
     * @throws CodeError
     *
     * @return void
     */
    protected function processToken(): void
    {
        $prevStatement = null;

        foreach ($this->useStatements as $useStatement) {
            if ($prevStatement && ($this->compareUseStatements($useStatement, $prevStatement) < 0)) {
                $exception = new CodeError(
                    static::CODE_INCORRECT_ORDER,
                    'Use statements should be sorted alphabetically. The first wrong one is %s.',
                    $useStatement->getPointer()
                );

                $exception
                    ->setPayload([$useStatement->getFullyQualifiedTypeName()])
                    ->isFixable(true);

                throw $exception;
            }

            $prevStatement = $useStatement;
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
     * @return int[]
     */
    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    /**
     * Removes the lines of the old statements.
     *
     * @param UseStatement $firstUseStatement
     *
     * @return void
     */
    private function removeOldUseStatements(UseStatement $firstUseStatement): void
    {
        $file = $this->getFile()->getBaseFile();
        $lastUseStatement = end($this->useStatements);
        $lastSemicolonPointer = TokenHelper::findNext($file, T_SEMICOLON, $lastUseStatement->getPointer());

        for ($i = $firstUseStatement->getPointer(); $i <= $lastSemicolonPointer; $i++) {
            $file->fixer->replaceToken($i, '');
        }
    }

    /**
     * Saves the use-property by the compare function.
     *
     * @return void
     */
    private function sortUseStatements(): void
    {
        uasort($this->useStatements, function (UseStatement $leftStatement, UseStatement $rightStatement) {
            return $this->compareUseStatements($leftStatement, $rightStatement);
        });
    }
}
