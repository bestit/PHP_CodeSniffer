<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\CodeSniffer\Helper\ClassHelper;
use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\ClassRegistrationTrait;
use const T_COMMA;
use const T_OPEN_CURLY_BRACKET;
use const T_SEMICOLON;
use const T_WHITESPACE;

/**
 * Sniffs if the uses in a class are correct.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class TraitUseDeclarationSniff extends AbstractSniff
{
    use ClassRegistrationTrait;

    /**
     * You MUST provide only one "use" per Line for importing traits etc. in classes.
     */
    public const CODE_MULTIPLE_TRAITS_PER_DECLARATION = 'MultipleTraitsPerDeclaration';

    /**
     * Readable error message.
     */
    private const MESSAGE_MULTIPLE_TRAITS_PER_DECLARATION = 'Multiple traits per use statement are forbidden.';

    /**
     * The use declarations positions of this "class".
     *
     * @var array
     */
    private $uses;

    /**
     * Returns false if there are no uses.
     *
     * @return bool
     */
    protected function areRequirementsMet(): bool
    {
        return (bool) $this->uses = ClassHelper::getTraitUsePointers($this->getFile(), $this->getStackPos());
    }

    /**
     * Checks the declaration of the given use position and registers and error if needed.
     *
     * @param int $usePos
     *
     * @return void
     */
    private function checkDeclaration(int $usePos): void
    {
        $file = $this->getFile()->getBaseFile();

        if (TokenHelper::findNextLocal($file, T_COMMA, $usePos + 1)) {
            $endPos = TokenHelper::findNext($file, [T_OPEN_CURLY_BRACKET, T_SEMICOLON], $usePos + 1);
            $tokens = $file->getTokens();

            if ($tokens[$endPos]['code'] === T_OPEN_CURLY_BRACKET) {
                $file->addError(
                    self::MESSAGE_MULTIPLE_TRAITS_PER_DECLARATION,
                    $usePos,
                    static::CODE_MULTIPLE_TRAITS_PER_DECLARATION
                );
            } else {
                $fix = $file->addFixableError(
                    self::MESSAGE_MULTIPLE_TRAITS_PER_DECLARATION,
                    $usePos,
                    static::CODE_MULTIPLE_TRAITS_PER_DECLARATION
                );

                if ($fix) {
                    $this->fixeUse($endPos, $usePos);
                }
            }
        }
    }

    /**
     * Fixes the given use position.
     *
     * @param int $endPos The end of the checked position.
     * @param int $usePos
     *
     * @return void
     */
    protected function fixeUse(int $endPos, int $usePos): void
    {
        $indentation = $this->getIndentationForFix($usePos);
        $file = $this->getFile()->getBaseFile();
        $fixer = $file->fixer;

        $fixer->beginChangeset();

        $commaPointers = TokenHelper::findNextAll($file, T_COMMA, $usePos + 1, $endPos);
        foreach ($commaPointers as $commaPos) {
            $pointerAfterComma = TokenHelper::findNextEffective($file, $commaPos + 1);
            $fixer->replaceToken($commaPos, ';' . $file->eolChar . $indentation . 'use ');
            for ($i = $commaPos + 1; $i < $pointerAfterComma; $i++) {
                $fixer->replaceToken($i, '');
            }
        }

        $fixer->endChangeset();
    }

    /**
     * Returns the needed indentation whitespace for fhe fixing of the uses.
     *
     * @param int $usePos
     *
     * @return string
     */
    private function getIndentationForFix(int $usePos): string
    {
        $file = $this->getFile()->getBaseFile();
        $indentation = '';
        $currentPointer = $usePos - 1;
        $tokens = $file->getTokens();

        while (
            $tokens[$currentPointer]['code'] === T_WHITESPACE &&
            $tokens[$currentPointer]['content'] !== $file->eolChar
        ) {
            $indentation .= $tokens[$currentPointer]['content'];
            $currentPointer--;
        }

        return $indentation;
    }

    /**
     * Processes the token.
     *
     * @return void
     */
    protected function processToken(): void
    {
        foreach ($this->uses as $use) {
            $this->checkDeclaration($use);
        }
    }
}
