<?php

namespace BestIt\Sniffs\DocTags\Fixtures\RequiredMethodTagsSniff\WithErrors;

/**
 * Class Testeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee
 *
 * This is an long class description.
 * Which is to be checked with ClassDocSniff.
 *
 * {@inheritdoc}
 *
 * teyst 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,.......................................................................
 * test123
 *
 * @author foo <bar@example.com>
 * @deprecated since 1.0.0. To be removed in 2.0.0.
 * @version 0.9.0
 */
class VoidReturn
{
    /**
     * Returns a test string.
     *
     * @param string $bar
     * @param null|string $baz
     */
    public function foo(string $bar = 'baz', $baz = null)
    {
        return;
    }
}
