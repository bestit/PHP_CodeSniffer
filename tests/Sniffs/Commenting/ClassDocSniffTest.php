<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\SniffTestCase;
use PHP_CodeSniffer\Files\File;

/**
 * Class ClassDocSniffTest
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class ClassDocSniffTest extends SniffTestCase
{
    /**
     * Test that the given files contain no errors.
     *
     * @dataProvider getCorrectFileList
     * @param string $file Provided file to test
     *
     * @return void
     */
    public function testCorrect(string $file)
    {
        $this->assertFileCorrect($file);
    }

    /**
     * Tests non fixable errors.
     *
     * @dataProvider getErrorData
     *
     * @param string $file Fixture file
     * @param string $error Error code
     * @param int[] $lines Lines where the error code occurs
     * @param array $sniffProperties Array of sniff properties
     *
     * @return void
     */
    public function testErrors(string $file, string $error, array $lines, array $sniffProperties = [])
    {
        $this->assertErrorsInFile($file, $error, $lines, $sniffProperties);
    }

    /**
     * Tests fixable errors.
     *
     * @dataProvider getFixableErrorData
     *
     * @param string $file Fixture file
     * @param string $error Error code
     * @param int[] $lines Lines where the error code occurs
     * @param array $sniffProperties Array of sniff properties
     *
     * @return void
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
            ClassDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND => [
                'NoImmediateDocFound.php',
                ClassDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND,
                [12]
            ],

            ClassDocSniff::CODE_COMMENT_NOT_MULTI_LINE => [
                'CommentNotMultiLine.php',
                ClassDocSniff::CODE_COMMENT_NOT_MULTI_LINE,
                [5]
            ],

            ClassDocSniff::CODE_NO_SUMMARY => [
                'NoSummary.php',
                ClassDocSniff::CODE_NO_SUMMARY,
                [5]
            ],

            ClassDocSniff::CODE_SUMMARY_TOO_LONG => [
                'SummaryTooLong.php',
                ClassDocSniff::CODE_SUMMARY_TOO_LONG,
                [6]
            ],

            ClassDocSniff::CODE_DESCRIPTION_TOO_LONG => [
                'DescriptionTooLong.php',
                ClassDocSniff::CODE_DESCRIPTION_TOO_LONG,
                [9]
            ],

            ClassDocSniff::CODE_DESCRIPTION_NOT_FOUND => [
                'DescriptionNotFound.php',
                ClassDocSniff::CODE_DESCRIPTION_NOT_FOUND,
                [6],
                [
                    'descriptionRequired' => true
                ]
            ]
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
            ClassDocSniff::CODE_SUMMARY_NOT_FIRST => [
                'SummaryNotFirst.php',
                ClassDocSniff::CODE_SUMMARY_NOT_FIRST,
                [7]
            ],

            ClassDocSniff::CODE_NO_LINE_AFTER_SUMMARY => [
                'NoLineAfterSummary.php',
                ClassDocSniff::CODE_NO_LINE_AFTER_SUMMARY,
                [6]
            ],

            ClassDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION => [
                'NoLineAfterDescription.php',
                ClassDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION,
                [9]
            ],

            ClassDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION => [
                'MuchLinesAfterDescription.php',
                ClassDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                [9]
            ],

            ClassDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION . '.WithoutTags' => [
                'MuchLinesAfterDescription.WithoutTags.php',
                ClassDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                [9]
            ],

            ClassDocSniff::CODE_SUMMARY_UC_FIRST => [
                'SummaryUcFirst.php',
                ClassDocSniff::CODE_SUMMARY_UC_FIRST,
                [6]
            ],

            ClassDocSniff::CODE_DESCRIPTION_UC_FIRST => [
                'DescriptionUcFirst.php',
                ClassDocSniff::CODE_DESCRIPTION_UC_FIRST,
                [8]
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
                ClassDocSniff::CODE_NO_IMMEDIATE_DOC_FOUND,
                ClassDocSniff::CODE_COMMENT_NOT_MULTI_LINE,
                ClassDocSniff::CODE_NO_SUMMARY,
                ClassDocSniff::CODE_SUMMARY_NOT_FIRST,
                ClassDocSniff::CODE_NO_LINE_AFTER_SUMMARY,
                ClassDocSniff::CODE_SUMMARY_TOO_LONG,
                ClassDocSniff::CODE_LINE_AFTER_SUMMARY_NOT_EMPTY,
                ClassDocSniff::CODE_DESCRIPTION_NOT_FOUND,
                ClassDocSniff::CODE_DESCRIPTION_TOO_LONG,
                ClassDocSniff::CODE_NO_LINE_AFTER_DESCRIPTION,
                ClassDocSniff::CODE_MUCH_LINES_AFTER_DESCRIPTION,
                ClassDocSniff::CODE_SUMMARY_UC_FIRST,
                ClassDocSniff::CODE_DESCRIPTION_UC_FIRST,
            ]
        );
    }
}
