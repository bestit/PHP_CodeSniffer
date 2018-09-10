<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\SniffTestCase;
use PHP_CodeSniffer\Files\File;

/**
 * Class ConstantDocSniffTest
 *
 * @package BestIt\Sniffs\Commenting
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ConstantDocSniffTest extends SniffTestCase
{
    /**
     * Test that the given files contain no errors.
     *
     * @param string $file Provided filename to test
     *
     * @return void
     *
     * @dataProvider getCorrectFileList
     */
    public function testCorrect(string $file)
    {
        $this->assertFileCorrect($file);
    }

    /**
     * Tests non fixable errors.
     *
     * @param string $file Fixture file
     * @param string $error Error code
     * @param int[] $lines Lines where the error code occurs
     * @param array $sniffProperties Array of sniff properties
     *
     * @return void
     *
     * @dataProvider getErrorData
     */
    public function testErrors(string $file, string $error, array $lines, array $sniffProperties = [])
    {
        $this->assertErrorsInFile($file, $error, $lines, $sniffProperties);
    }

    /**
     * Tests fixable errors.
     *
     * @param string $file Fixture file
     * @param string $error Error code
     * @param int[] $lines Lines where the error code occurs
     * @param array $sniffProperties Array of sniff properties
     *
     * @return void
     *
     * @dataProvider getFixableErrorData
     */
    public function testFixableErrors(string $file, string $error, array $lines, array $sniffProperties = [])
    {
        $this->assertFixableErrorsInFile($file, $error, $lines, $sniffProperties);
    }

    /**
     * Returns data for not fixable errors.
     *
     * @return array List of error data
     */
    public function getErrorData(): array
    {
        $fixableErrors = $this->getFixableErrorData();

        $errors =  [
            ConstantDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND => [
                'NoImmediateDocFound.php',
                ConstantDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND,
                [22]
            ],

            ConstantDocSniff::CODE_COMMENT_NOT_MULTI_LINE => [
                'CommentNotMultiLine.php',
                ConstantDocSniff::CODE_COMMENT_NOT_MULTI_LINE,
                [13]
            ],

            ConstantDocSniff::CODE_NO_SUMMARY => [
                'NoSummary.php',
                ConstantDocSniff::CODE_NO_SUMMARY,
                [13]
            ],

            ConstantDocSniff::CODE_SUMMARY_TOO_LONG => [
                'SummaryTooLong.php',
                ConstantDocSniff::CODE_SUMMARY_TOO_LONG,
                [14]
            ],

            ConstantDocSniff::CODE_DESCRIPTION_TOO_LONG => [
                'DescriptionTooLong.php',
                ConstantDocSniff::CODE_DESCRIPTION_TOO_LONG,
                [16, 17]
            ],

            ConstantDocSniff::CODE_DESCRIPTION_NOT_FOUND => [
                'DescriptionNotFound.php',
                ConstantDocSniff::CODE_DESCRIPTION_NOT_FOUND,
                [14],
                [
                    'descriptionRequired' => true
                ]
            ],

            ConstantDocSniff::CODE_TAG_NOT_ALLOWED => [
                'TagNotAllowed.php',
                ConstantDocSniff::CODE_TAG_NOT_ALLOWED,
                [
                    24,
                    25,
                    26,
                    27,
                    28,
                    29,
                    30,
                    31,
                    32,
                    33,
                    34,
                    35,
                    36,
                    37,
                    38,
                    39,
                    40,
                    41,
                    42,
                    43,
                    44,
                    45,
                    46,
                    47
                ]
            ],

            ConstantDocSniff::CODE_TAG_OCCURRENCE_MIN => [
                'TagOccurrenceMin.php',
                ConstantDocSniff::CODE_TAG_OCCURRENCE_MIN,
                [13]
            ],

            ConstantDocSniff::CODE_TAG_OCCURRENCE_MAX => [
                'TagOccurrenceMax.php',
                ConstantDocSniff::CODE_TAG_OCCURRENCE_MAX,
                [13]
            ],
        ];

        return array_merge(
            $fixableErrors,
            $errors
        );
    }

    /**
     * Returns data for fixable errors.
     *
     * @return array List of fixable error data
     */
    public function getFixableErrorData(): array
    {
        return [
            ConstantDocSniff::CODE_SUMMARY_NOT_FIRST => [
                'SummaryNotFirst.php',
                ConstantDocSniff::CODE_SUMMARY_NOT_FIRST,
                [15]
            ],

            ConstantDocSniff::CODE_NO_LINE_AFTER_SUMMARY => [
                'NoLineAfterSummary.php',
                ConstantDocSniff::CODE_NO_LINE_AFTER_SUMMARY,
                [14]
            ],

            ConstantDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION => [
                'NoLineAfterDescription.php',
                ConstantDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION,
                [17]
            ],

            ConstantDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION => [
                'MuchLinesAfterDescription.php',
                ConstantDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                [17]
            ],

            ConstantDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION . '.WithoutTags' => [
                'MuchLinesAfterDescription.WithoutTags.php',
                ConstantDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                [17]
            ],

            ConstantDocSniff::CODE_SUMMARY_UC_FIRST => [
                'SummaryUcFirst.php',
                ConstantDocSniff::CODE_SUMMARY_UC_FIRST,
                [14]
            ],

            ConstantDocSniff::CODE_DESCRIPTION_UC_FIRST => [
                'DescriptionUcFirst.php',
                ConstantDocSniff::CODE_DESCRIPTION_UC_FIRST,
                [16]
            ],

            ConstantDocSniff::CODE_MUCH_LINES_AFTER_TAG => [
                'MuchLinesAfterTag.php',
                ConstantDocSniff::CODE_MUCH_LINES_AFTER_TAG,
                [19]
            ],
        ];
    }

    /**
     * Checks the given file with defined error codes.
     *
     * @param string $file Filename of the fixture
     * @param array $sniffProperties Array of sniff properties
     *
     * @return File The php cs file
     */
    protected function checkSniffFile(string $file, array $sniffProperties = []): File
    {
        return $this->checkFile(
            $file,
            $sniffProperties,
            [
                ConstantDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND,
                ConstantDocSniff::CODE_COMMENT_NOT_MULTI_LINE,
                ConstantDocSniff::CODE_NO_SUMMARY,
                ConstantDocSniff::CODE_SUMMARY_NOT_FIRST,
                ConstantDocSniff::CODE_NO_LINE_AFTER_SUMMARY,
                ConstantDocSniff::CODE_SUMMARY_TOO_LONG,
                ConstantDocSniff::CODE_LINE_AFTER_SUMMARY_NOT_EMPTY,
                ConstantDocSniff::CODE_DESCRIPTION_NOT_FOUND,
                ConstantDocSniff::CODE_DESCRIPTION_TOO_LONG,
                ConstantDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION,
                ConstantDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                ConstantDocSniff::CODE_TAG_NOT_ALLOWED,
                ConstantDocSniff::CODE_TAG_OCCURRENCE_MIN,
                ConstantDocSniff::CODE_TAG_OCCURRENCE_MAX,
                ConstantDocSniff::CODE_TAG_WRONG_POSITION,
                ConstantDocSniff::CODE_SUMMARY_UC_FIRST,
                ConstantDocSniff::CODE_DESCRIPTION_UC_FIRST,
                ConstantDocSniff::CODE_NO_LINE_AFTER_TAG,
                ConstantDocSniff::CODE_MUCH_LINES_AFTER_TAG,
            ]
        );
    }
}
