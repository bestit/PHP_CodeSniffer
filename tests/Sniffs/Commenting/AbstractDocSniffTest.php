<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use PHP_CodeSniffer\Sniffs\Sniff;
use function defined;

/**
 * Basic test for the summary sniffs.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package  BestIt\Sniffs\Commenting
 */
abstract class AbstractDocSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestTokenRegistrationTrait;

    /**
     * The tested class.
     *
     * We use this var to reduce the hard dependencies on internals from a specific slevomat version.
     *
     * @var Sniff|void
     */
    protected $fixture;

    /**
     * Returns the names of the required constants.
     *
     * @return array
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_NO_LINE_AFTER_DOC_COMMENT' => ['CODE_NO_LINE_AFTER_DOC_COMMENT', 'NoLineAfterDocComment'],
            'CODE_NO_SUMMARY' => ['CODE_NO_SUMMARY', 'NoSummary'],
            'CODE_SUMMARY_TOO_LONG' => ['CODE_SUMMARY_TOO_LONG', 'SummaryTooLong'],
            'CODE_DOC_COMMENT_UC_FIRST' => ['CODE_DOC_COMMENT_UC_FIRST', 'DocCommentUcFirst'],
        ];
    }

    /**
     * Checks if the api is extended.
     *
     * @dataProvider getRequiredConstantAsserts
     * @param string $constant The name of the constant.
     *
     * @return void
     */
    public function testRequiredConstants(string $constant): void
    {
        static::assertTrue(

            defined(get_class($this->fixture) . '::' . $constant),
            'Constant ' . $constant . ' is missing.'
        );
    }
}
