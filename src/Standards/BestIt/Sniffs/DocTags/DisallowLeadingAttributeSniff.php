<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\CodeSniffer\CodeError;
use BestIt\CodeSniffer\CodeWarning;
use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\Sniffs\AbstractSniff;
use function compact;
use function in_array;
use function sprintf;
use function str_repeat;
use const T_ATTRIBUTE;
use const T_ATTRIBUTE_END;
use const T_CLOSE_SQUARE_BRACKET;
use const T_DOC_COMMENT_CLOSE_TAG;

class DisallowLeadingAttributeSniff extends AbstractSniff
{
    public const CODE_WRONG_ATTRIBUTE_POSITION = 'WrongAttrPos';

    private const MESSAGE_WRONG_ATTRIBUTE_POSITION = 'Please place the PHP Attribute under the doc block.';

    private function addAttributeAfterDocBlock(
        int $docTagClosingPos,
        string $collectedAttrContent,
    ): void {
        $this->getFile()->fixer->addContent(
            $docTagClosingPos,
            sprintf(
                "\n" . str_repeat(' ', ($this->tokens[$docTagClosingPos]['level']) * 4) . '%s',
                trim($collectedAttrContent),
            ),
        );
    }

    protected function fixDefaultProblem(CodeWarning $exception): void
    {
        $prevTokenPos = @$exception->getPayload()['prevTokenPos'];

        if ($prevTokenPos) {
            $this->switchPositions($prevTokenPos);
        }
    }

    protected function processToken(): void
    {
        $prevTokenPos = TokenHelper::findPreviousEffective($this->getFile(), $this->getStackPos() - 1);
        $undesiredPrevTokenCodes = [T_ATTRIBUTE_END, T_CLOSE_SQUARE_BRACKET];

        if ($prevTokenPos && in_array($this->tokens[$prevTokenPos]['code'], $undesiredPrevTokenCodes, true)) {
            $error = new CodeError(
                static::CODE_WRONG_ATTRIBUTE_POSITION,
                self::MESSAGE_WRONG_ATTRIBUTE_POSITION,
                $prevTokenPos,
            );

            $error
                ->setPayload(compact('prevTokenPos'))
                ->isFixable(true);

            throw $error;
        }
    }

    public function register(): array
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    private function removeWrongAttribute(
        int $attrOpenTokenPos,
    ): string {
        $collectedAttrContent = '';
        $docTagOpeningPos = $this->getStackPos();
        $file = $this->getFile();
        $file->fixer->beginChangeset();

        for ($attrTokenPos = $attrOpenTokenPos; $attrTokenPos < $docTagOpeningPos; ++$attrTokenPos) {
            if ($attrTokenPos < $docTagOpeningPos - 1) {
                $collectedAttrContent .= $this->tokens[$attrTokenPos]['content'];
            }

            $file->fixer->replaceToken($attrTokenPos, '');
        }

        return $collectedAttrContent;
    }

    private function switchPositions(int $attrCloseTokenPos): void
    {
        $file = $this->getFile();

        $attrOpenTokenPos = TokenHelper::findPrevious(
            $file,
            [T_ATTRIBUTE],
            $attrCloseTokenPos - 1,
        );

        $docTagClosingPos = TokenHelper::findNext(
            $file,
            [T_DOC_COMMENT_CLOSE_TAG],
            $this->getStackPos() + 1,
        );

        if ($attrOpenTokenPos && $docTagClosingPos) {
            $file->fixer->beginChangeset();

            $collectedAttrContent = $this->removeWrongAttribute($attrOpenTokenPos);

            $this->addAttributeAfterDocBlock($docTagClosingPos, $collectedAttrContent);

            $file->fixer->endChangeset();
        }
    }
}
