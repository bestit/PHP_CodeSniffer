<?php

class ForbiddenFunctionSizeofTestButSuppressed
{
    public function __construct()
    {
        /** @phpcsSuppress BestIt.Functions.ForbiddenFunctions.DiscouragedWithAlternative */
        sizeof([]);
    }
}