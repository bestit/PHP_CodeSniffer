<?php

class ForbiddenFunctionDieTestButSuppressed
{
    public function __construct()
    {
        /** @phpcsSuppress BestIt.Functions.ForbiddenFunctions.DiscouragedWithAlternative */
        die('foobar');
    }
}