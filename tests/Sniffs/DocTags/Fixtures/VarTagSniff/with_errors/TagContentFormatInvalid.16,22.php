<?php

namespace Test;

use stdClass;

/**
 * Class WithNamespace.
 *
 * @package Test
 */
class WithTags {
    /**
     * The used file.
     *
     * @var
     */
    protected $file;

    public function __construct()
    {
        /** @var */
        $foo = 'bar';
    }
}