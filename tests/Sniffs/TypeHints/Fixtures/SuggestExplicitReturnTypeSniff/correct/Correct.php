<?php

declare(strict_types=1);

namespace BestIt\Sniffs\TypeHints\Fixtures\SuggestExplicitReturnTypeSniff\correct;

class Correct
{
    /**
     * @phpcsSuppress BestIt.TypeHints.SuggestExplicitReturnType.MixedType
     */
    public function mixed(): mixed
    {

    }

    /**
     * @phpcsSuppress BestIt.TypeHints.SuggestExplicitReturnType.MixedType
     */
    public function mixed(): mixed
    {

    }

    public function string(): string
    {

    }
}