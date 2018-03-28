<?php

declare(strict_types=1);

namespace Tests\BestIt\Sniffs\Commenting;

use BestIt\Sniffs\Commenting\MethodDocSniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer_File;
use Tests\BestIt\Sniffs\Filename;
use Tests\BestIt\SniffTestCase;

/**
 * Class MethodDocSniffTest
 *
 * @package Tests\BestIt\Sniffs\Commenting
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class MethodDocSniffTest extends SniffTestCase
{
    /**
     * Test that the given files contain no errors.
     *
     * @param string $file Provided file to test
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
            MethodDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND => [
                'NoImmediateDocFound.php',
                MethodDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND,
                [25, 42]
            ],

            MethodDocSniff::CODE_COMMENT_NOT_MULTI_LINE => [
                'CommentNotMultiLine.php',
                MethodDocSniff::CODE_COMMENT_NOT_MULTI_LINE,
                [13]
            ],

            MethodDocSniff::CODE_NO_SUMMARY => [
                'NoSummary.php',
                MethodDocSniff::CODE_NO_SUMMARY,
                [13, 24]
            ],

            MethodDocSniff::CODE_SUMMARY_TOO_LONG => [
                'SummaryTooLong.php',
                MethodDocSniff::CODE_SUMMARY_TOO_LONG,
                [14, 30]
            ],

            MethodDocSniff::CODE_DESCRIPTION_TOO_LONG => [
                'DescriptionTooLong.php',
                MethodDocSniff::CODE_DESCRIPTION_TOO_LONG,
                [16, 32, 33]
            ],

            MethodDocSniff::CODE_DESCRIPTION_NOT_FOUND => [
                'DescriptionNotFound.php',
                MethodDocSniff::CODE_DESCRIPTION_NOT_FOUND,
                [14, 27],
                [
                    'descriptionRequired' => true
                ]
            ],

            MethodDocSniff::CODE_TAG_NOT_ALLOWED => [
                'TagNotAllowed.php',
                MethodDocSniff::CODE_TAG_NOT_ALLOWED,
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

            MethodDocSniff::CODE_TAG_OCCURRENCE_MIN => [
                'TagOccurrenceMin.php',
                MethodDocSniff::CODE_TAG_OCCURRENCE_MIN,
                [13]
            ],

            MethodDocSniff::CODE_TAG_OCCURRENCE_MAX => [
                'TagOccurrenceMax.php',
                MethodDocSniff::CODE_TAG_OCCURRENCE_MAX,
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
            MethodDocSniff::CODE_SUMMARY_NOT_FIRST => [
                'SummaryNotFirst.php',
                MethodDocSniff::CODE_SUMMARY_NOT_FIRST,
                [15, 32]
            ],

            MethodDocSniff::CODE_NO_LINE_AFTER_SUMMARY => [
                'NoLineAfterSummary.php',
                MethodDocSniff::CODE_NO_LINE_AFTER_SUMMARY,
                [14, 29]
            ],

            MethodDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION => [
                'NoLineAfterDescription.php',
                MethodDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION,
                [17, 32]
            ],

            MethodDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION => [
                'MuchLinesAfterDescription.php',
                MethodDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                [17, 35]
            ],

            MethodDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION . '.WithoutTags' => [
                'MuchLinesAfterDescription.WithoutTags.php',
                MethodDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                [17]
            ],

            MethodDocSniff::CODE_SUMMARY_UC_FIRST => [
                'SummaryUcFirst.php',
                MethodDocSniff::CODE_SUMMARY_UC_FIRST,
                [14, 30]
            ],

            MethodDocSniff::CODE_DESCRIPTION_UC_FIRST => [
                'DescriptionUcFirst.php',
                MethodDocSniff::CODE_DESCRIPTION_UC_FIRST,
                [16, 32]
            ],

            MethodDocSniff::CODE_NO_LINE_AFTER_TAG => [
                'NoLineAfterTag.php',
                MethodDocSniff::CODE_NO_LINE_AFTER_TAG,
                [20]
            ],

            MethodDocSniff::CODE_MUCH_LINES_AFTER_TAG => [
                'MuchLinesAfterTag.php',
                MethodDocSniff::CODE_MUCH_LINES_AFTER_TAG,
                [19, 22, 27]
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
                MethodDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND,
                MethodDocSniff::CODE_COMMENT_NOT_MULTI_LINE,
                MethodDocSniff::CODE_NO_SUMMARY,
                MethodDocSniff::CODE_SUMMARY_NOT_FIRST,
                MethodDocSniff::CODE_NO_LINE_AFTER_SUMMARY,
                MethodDocSniff::CODE_SUMMARY_TOO_LONG,
                MethodDocSniff::CODE_LINE_AFTER_SUMMARY_NOT_EMPTY,
                MethodDocSniff::CODE_DESCRIPTION_NOT_FOUND,
                MethodDocSniff::CODE_DESCRIPTION_TOO_LONG,
                MethodDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION,
                MethodDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                MethodDocSniff::CODE_TAG_NOT_ALLOWED,
                MethodDocSniff::CODE_TAG_OCCURRENCE_MIN,
                MethodDocSniff::CODE_TAG_OCCURRENCE_MAX,
                MethodDocSniff::CODE_TAG_WRONG_POSITION,
                MethodDocSniff::CODE_SUMMARY_UC_FIRST,
                MethodDocSniff::CODE_DESCRIPTION_UC_FIRST,
                MethodDocSniff::CODE_NO_LINE_AFTER_TAG,
                MethodDocSniff::CODE_MUCH_LINES_AFTER_TAG,
            ]
        );
    }
}
