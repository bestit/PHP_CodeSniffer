<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\Fixtures\ForbidDoubledWhitespaceSniff\correct;

class Correct
{
    /**
     * DuplicateSpaces constructor.
     *
     * @param string $param1 This is a test.
     * @param string $parameter2 And this as well.
     */
    public function __construct(
        $param1 = 'foo',
        $parameter2 = 'bar',
    ) {
        $parameter2 = $param1;
        $param1 = 'baz';

        $lineBreak =
            'hello world';
    }
}