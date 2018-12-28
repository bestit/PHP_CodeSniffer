<?php

class ForbiddenFunctionEvalTestButSuppressed
{
    /**
     * ForbiddenFunctionEvalTest constructor.
     */
    public function __construct()
    {
        /** @phpcsSuppress BestIt.Functions.ForbiddenFunctions.Discouraged */
        eval('foobar');
    }
}