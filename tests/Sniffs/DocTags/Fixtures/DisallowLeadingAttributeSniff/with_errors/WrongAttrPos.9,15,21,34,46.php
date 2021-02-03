<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags\Fixtures\DisallowLeadingAttributeSniff\correct;

use BestIt\Sniffs\DocTags\Fixtures\DisallowLeadingAttributeSniff\TestAttribute;

#[TestAttribute('class')]
/**
 * php doc test
 */
class WrongAttrPos
{
    #[TestAttribute]
    /**
     * php doc test
     */
    const TEST = 'foobar';

    #[TestAttribute]
    /**
     * php doc test
     */
    private $test = 'bar';

    public function __construct(#[TestAttribute('prop promotion')] private string $anotherTest = '')
    {
    }

    #[
        TestAttribute(),
        TestAttribute('next attr')
    ]
    /**
     * php doc test
     */
    public function test(#[TestAttribute] string $bar = ''): void
    {
        $closure = #[TestAttribute] fn($bar) => 'best it is the best ;)';

        echo $closure($bar);
    }
}

#[TestAttribute('function')]
/**
 * php doc test
 */
function correctAttributeTest() {
    return new #[TestAttribute('anon class')] class () extends Correct {

    };
}