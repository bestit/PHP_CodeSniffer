<?php

namespace Test;

use stdClass;

/**
 * Class WithNamespace.
 *
 * @package Test
 */
class WithTags
{
    /**
     * A test with arrays.
     *
     * @var string[]
     */
    public $array = ['foo', 'bar'];

    /**
     * The used file.
     *
     * @var stdClass|void
     */
    protected $file;

    /**
     * Code that the tag content format is invalid.
     *
     * @var string
     */
    public const CODE_TAG_CONTENT_FORMAT_INVALID = 'TagContentFormatInvalid';

    public function __construct()
    {
        /** @var string $foo */
        $foo = 'bar';

        /** @var string */
        $bar = 'baz';
    }
}
