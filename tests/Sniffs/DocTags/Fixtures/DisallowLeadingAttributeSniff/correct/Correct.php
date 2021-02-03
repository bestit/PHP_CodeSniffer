<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags\Fixtures\DisallowLeadingAttributeSniff\correct;

use BestIt\Sniffs\DocTags\Fixtures\DisallowLeadingAttributeSniff\TestAttribute;

/**
 * php doc test
 */
#[TestAttribute('class')]
class Correct
{
    /**
     * php doc test
     */
    #[TestAttribute]
    const TEST = 'foobar';

    /**
     * php doc test
     */
    #[TestAttribute]
    private $test = 'bar';

    public function __construct(#[TestAttribute('prop promotion')] private string $anotherTest = '')
    {
    }

    /**
     * php doc test
     */
    #[
        TestAttribute(),
        TestAttribute('next attr')
    ]
    public function test(#[TestAttribute] string $bar = ''): void
    {
        $closure = #[TestAttribute] fn($bar) => 'best it is the best ;)';

        echo $closure($bar);
    }
}

/**
 * php doc test
 */
#[TestAttribute('function')]
function correctAttributeTest() {
    /**
     * php doc test
     */
    return new #[TestAttribute('anon class')] class () extends Correct {

    };
}