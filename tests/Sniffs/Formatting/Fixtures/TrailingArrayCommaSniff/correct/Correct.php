<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting\Fixtures\TrailingArrayCommaSniff;

class Correct
{
    private $inline = ['foo', 'bar', 'baz'];

    private $multi = [
        'foo',
        'bar',
        'baz',
    ];

    public function inline()
    {
        return ['foo', 'bar', 'baz'];
    }

    public function multi()
    {
        return [
            'foo',
            'bar',
            'baz',
        ];
    }
}