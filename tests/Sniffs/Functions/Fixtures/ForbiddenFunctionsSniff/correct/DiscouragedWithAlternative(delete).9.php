<?php

class ForbiddenFunctionDeleteTestButSuppressed
{
    /**
     * ForbiddenFunctionDeleteTestButSuppressed constructor.
     */
    public function __construct()
    {
        $tests = [];

        /** @phpcsSuppress BestIt.Functions.ForbiddenFunctions.DiscouragedWithAlternative */
        delete($tests[0]);
    }
}