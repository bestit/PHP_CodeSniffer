<?php

declare(strict_types = 1);

namespace Tests\BestIt\Sniffs\Commenting;

use BestIt\Sniffs\Commenting\PropertyDocSniff;
use PHP_CodeSniffer\Files\File;
use Tests\BestIt\SniffTestCase;

/**
 * Class PropertyDocSniffTest
 *
 * @package Tests\BestIt\Sniffs\Commenting
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class PropertyDocSniffTest extends SniffTestCase
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
            PropertyDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND => [
                'NoImmediateDocFound.php',
                PropertyDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND,
                [22]
            ],

            PropertyDocSniff::CODE_COMMENT_NOT_MULTI_LINE => [
                'CommentNotMultiLine.php',
                PropertyDocSniff::CODE_COMMENT_NOT_MULTI_LINE,
                [13]
            ],

            PropertyDocSniff::CODE_NO_SUMMARY => [
                'NoSummary.php',
                PropertyDocSniff::CODE_NO_SUMMARY,
                [13]
            ],

            PropertyDocSniff::CODE_SUMMARY_TOO_LONG => [
                'SummaryTooLong.php',
                PropertyDocSniff::CODE_SUMMARY_TOO_LONG,
                [14]
            ],

            PropertyDocSniff::CODE_DESCRIPTION_TOO_LONG => [
                'DescriptionTooLong.php',
                PropertyDocSniff::CODE_DESCRIPTION_TOO_LONG,
                [16, 17]
            ],

            PropertyDocSniff::CODE_DESCRIPTION_NOT_FOUND => [
                'DescriptionNotFound.php',
                PropertyDocSniff::CODE_DESCRIPTION_NOT_FOUND,
                [14],
                [
                    'descriptionRequired' => true
                ]
            ],

            PropertyDocSniff::CODE_TAG_NOT_ALLOWED => [
                'TagNotAllowed.php',
                PropertyDocSniff::CODE_TAG_NOT_ALLOWED,
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

            PropertyDocSniff::CODE_TAG_OCCURRENCE_MIN => [
                'TagOccurrenceMin.php',
                PropertyDocSniff::CODE_TAG_OCCURRENCE_MIN,
                [13]
            ],

            PropertyDocSniff::CODE_TAG_OCCURRENCE_MAX => [
                'TagOccurrenceMax.php',
                PropertyDocSniff::CODE_TAG_OCCURRENCE_MAX,
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
     * @return array List with fixable error data
     */
    public function getFixableErrorData(): array
    {
        return [
            PropertyDocSniff::CODE_SUMMARY_NOT_FIRST => [
                'SummaryNotFirst.php',
                PropertyDocSniff::CODE_SUMMARY_NOT_FIRST,
                [15]
            ],

            PropertyDocSniff::CODE_NO_LINE_AFTER_SUMMARY => [
                'NoLineAfterSummary.php',
                PropertyDocSniff::CODE_NO_LINE_AFTER_SUMMARY,
                [14]
            ],

            PropertyDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION => [
                'NoLineAfterDescription.php',
                PropertyDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION,
                [17]
            ],

            PropertyDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION => [
                'MuchLinesAfterDescription.php',
                PropertyDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                [17]
            ],

            PropertyDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION . '.WithoutTags' => [
                'MuchLinesAfterDescription.WithoutTags.php',
                PropertyDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                [17]
            ],

            PropertyDocSniff::CODE_SUMMARY_UC_FIRST => [
                'SummaryUcFirst.php',
                PropertyDocSniff::CODE_SUMMARY_UC_FIRST,
                [14]
            ],

            PropertyDocSniff::CODE_DESCRIPTION_UC_FIRST => [
                'DescriptionUcFirst.php',
                PropertyDocSniff::CODE_DESCRIPTION_UC_FIRST,
                [16]
            ],

            PropertyDocSniff::CODE_MUCH_LINES_AFTER_TAG => [
                'MuchLinesAfterTag.php',
                PropertyDocSniff::CODE_MUCH_LINES_AFTER_TAG,
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
                PropertyDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND,
                PropertyDocSniff::CODE_COMMENT_NOT_MULTI_LINE,
                PropertyDocSniff::CODE_NO_SUMMARY,
                PropertyDocSniff::CODE_SUMMARY_NOT_FIRST,
                PropertyDocSniff::CODE_NO_LINE_AFTER_SUMMARY,
                PropertyDocSniff::CODE_SUMMARY_TOO_LONG,
                PropertyDocSniff::CODE_LINE_AFTER_SUMMARY_NOT_EMPTY,
                PropertyDocSniff::CODE_DESCRIPTION_NOT_FOUND,
                PropertyDocSniff::CODE_DESCRIPTION_TOO_LONG,
                PropertyDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION,
                PropertyDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                PropertyDocSniff::CODE_TAG_NOT_ALLOWED,
                PropertyDocSniff::CODE_TAG_OCCURRENCE_MIN,
                PropertyDocSniff::CODE_TAG_OCCURRENCE_MAX,
                PropertyDocSniff::CODE_TAG_WRONG_POSITION,
                PropertyDocSniff::CODE_SUMMARY_UC_FIRST,
                PropertyDocSniff::CODE_DESCRIPTION_UC_FIRST,
                PropertyDocSniff::CODE_NO_LINE_AFTER_TAG,
                PropertyDocSniff::CODE_MUCH_LINES_AFTER_TAG,
            ]
        );
    }
}
