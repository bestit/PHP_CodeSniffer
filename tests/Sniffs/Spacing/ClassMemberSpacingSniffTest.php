<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Spacing;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_INTERFACE;
use const T_TRAIT;

/**
 * Test ClassMemberSpacingSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Spacing
 */
class ClassMemberSpacingSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestTokenRegistrationTrait;
    use TestRequiredConstantsTrait;

    /**
     * Which tokens are expected?
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_CLASS,
            T_TRAIT,
            T_INTERFACE,
            T_ANON_CLASS,
        ];
    }

    /**
     * Checks which constants are required.
     *
     * @return iterable
     */
    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS' => [
                'CODE_INCORRECT_COUNT_OF_BLANK_LINES_BETWEEN_MEMBERS',
                'IncorrectCountOfBlankLinesBetweenMembers',
            ],
        ];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new ClassMemberSpacingSniff();
    }
}
