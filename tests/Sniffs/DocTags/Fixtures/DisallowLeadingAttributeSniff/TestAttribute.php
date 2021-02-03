<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags\Fixtures\DisallowLeadingAttributeSniff;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class TestAttribute
{
    public function __construct(private string $value = 'best it')
    {
    }
}
