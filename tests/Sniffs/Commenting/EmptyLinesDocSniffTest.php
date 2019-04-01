<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use PHP_CodeSniffer\Util\Tokens;

if (!class_exists(Tokens::class, false)) {
    spl_autoload_call(Tokens::class);
}

/**
 * Class MultipleReturnSniffTest.
 *
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 *
 * @package BestIt\Sniffs\Commenting
 */
class EmptyLinesDocSniffTest extends SniffTestCase
{
    use TestTokenRegistrationTrait;
    use TestRequiredConstantsTrait;
    use DefaultSniffIntegrationTestTrait;

    /**
     * Get the expected tokens.
     *
     * @return array
     */
    protected function getExpectedTokens(): array
    {
        return [
            T_DOC_COMMENT_OPEN_TAG
        ];
    }

    /**
     * Get required constant asserts.
     *
     * @return array
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_EMPTY_LINES_FOUND' => ['CODE_EMPTY_LINES_FOUND', 'EmptyLinesFound'],
        ];
    }

    /**
     * Set up the sniff.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fixture = new EmptyLinesDocSniff();
    }
}
