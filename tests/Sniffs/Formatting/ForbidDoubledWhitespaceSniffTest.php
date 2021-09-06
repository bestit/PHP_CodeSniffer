<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Formatting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use SlevomatCodingStandard\Sniffs\Whitespaces\DuplicateSpacesSniff;

/**
 * Test ForbidDoubledWhitespaceSniff
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Formatting
 */
class ForbidDoubledWhitespaceSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;

    /**
     * Checks the required constants.
     *
     * @return iterable
     */
    public function getRequiredConstantAsserts(): iterable
    {
        return [
            'CODE_DUPLICATE_SPACES' => [
                'CODE_DUPLICATE_SPACES',
                'DuplicateSpaces',
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

        $this->fixture = new ForbidDoubledWhitespaceSniff();
    }

    /**
     * Checks the inheritance of the class.
     *
     * @return void
     */
    public function testInstance(): void
    {
        static::assertInstanceOf(
            DuplicateSpacesSniff::class,
            $this->fixture,
        );
    }
}
