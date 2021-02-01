<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints;

use BestIt\CodeSniffer\CodeWarning;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\SuppressingTrait;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHint;

class SuggestExplicitReturnTypeSniff extends AbstractSniff
{
    use SuppressingTrait;

    public const CODE_MIXED_TYPE = 'MixedType';

    private const MESSAGE_MIXED_TYPE = 'We suggest that you avoid the "mixed" type and declare the ' .
        'required types in detail.';

    private function isMixedTypeHint(): bool
    {
        $typeHint = FunctionHelper::findReturnTypeHint($this->getFile(), $this->getStackPos());

        return ($typeHint instanceof TypeHint) && ($typeHint->getTypeHint() === 'mixed');
    }

    protected function processToken(): void
    {
        if ($this->isMixedTypeHint() && !$this->isSniffSuppressed(self::CODE_MIXED_TYPE)) {
            throw (new CodeWarning(static::CODE_MIXED_TYPE, self::MESSAGE_MIXED_TYPE, $this->stackPos))
                ->setToken($this->token);
        }
    }

    public function register(): array
    {
        return TokenHelper::$functionTokenCodes;
    }
}
