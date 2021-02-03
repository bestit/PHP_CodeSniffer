<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_DOC_COMMENT_OPEN_TAG;

class DisallowLeadingAttributeSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    protected function getExpectedTokens(): array
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    public function getRequiredConstantAsserts(): iterable
    {
        return ['CODE_WRONG_ATTRIBUTE_POSITION' => ['CODE_WRONG_ATTRIBUTE_POSITION', 'WrongAttrPos']];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new DisallowLeadingAttributeSniff();
    }
}
