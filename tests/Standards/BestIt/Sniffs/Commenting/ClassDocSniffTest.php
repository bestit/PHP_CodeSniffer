<?php

declare(strict_types=1);

namespace Tests\BestIt\Sniffs\Commenting;

use BestIt\AbstractDocSniff;
use BestIt\Sniffs\Commenting\ClassDocSniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer_File;
use Tests\BestIt\Sniffs\Filename;
use Tests\BestIt\SniffTestCase;

/**
 * Class ClassDocSniffTest
 *
 * @package Tests\BestIt\Sniffs\Commenting
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
class ClassDocSniffTest extends SniffTestCase
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
            ],

            ClassDocSniff::CODE_TAG_NOT_ALLOWED => [
                'TagNotAllowed.php',
                ClassDocSniff::CODE_TAG_NOT_ALLOWED,
                [
                    20,
                    21,
                    22,
                    23,
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
                    44
                ]
            ],

            ClassDocSniff::CODE_TAG_OCCURRENCE_MIN => [
                'TagOccurrenceMin.php',
                ClassDocSniff::CODE_TAG_OCCURRENCE_MIN,
                [5]
            ],

            'SummaryWithoutAnyTags' => [
                'SummaryOnly.php',
                ClassDocSniff::CODE_TAG_OCCURRENCE_MIN,
                [5]
            ],

            'Tag.Author.Empty' => [
                'Tag.Author.Empty.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [9]
            ],

            'Tag.Author.OnlyFirstname' => [
                'Tag.Author.OnlyFirstname.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [9]
            ],

            'Tag.Author.OnlyFullname' => [
                'Tag.Author.OnlyFullname.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [9]
            ],

            'Tag.Author.WrongEmail1' => [
                'Tag.Author.WrongEmail1.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [9]
            ],

            'Tag.Author.WrongEmail2' => [
                'Tag.Author.WrongEmail2.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [9]
            ],

            'Tag.Author.WrongEmail3' => [
                'Tag.Author.WrongEmail3.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [9]
            ],

            'Tag.Author.WrongEmail4' => [
                'Tag.Author.WrongEmail4.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [9]
            ],

            'Tag.Package.WithWhitespace' => [
                'Tag.Package.WithWhitespace.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [8]
            ],

            'Tag.Package.MultipleBackslash' => [
                'Tag.Package.MultipleBackslash.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [8]
            ],

            'Tag.Package.StartingBackslash' => [
                'Tag.Package.StartingBackslash.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [8]
            ],

            'Tag.Deprecated.Empty' => [
                'Tag.Deprecated.Empty.php',
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID,
                [11]
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

            ClassDocSniff::CODE_MUCH_LINES_AFTER_TAG => [
                'MuchLinesAfterTag.php',
                ClassDocSniff::CODE_MUCH_LINES_AFTER_TAG,
                [16, 19, 23, 28]
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
                ClassDocSniff::CODE_TAG_NOT_ALLOWED,
                ClassDocSniff::CODE_TAG_OCCURRENCE_MIN,
                ClassDocSniff::CODE_TAG_OCCURRENCE_MAX,
                ClassDocSniff::CODE_TAG_WRONG_POSITION,
                ClassDocSniff::CODE_SUMMARY_UC_FIRST,
                ClassDocSniff::CODE_DESCRIPTION_UC_FIRST,
                ClassDocSniff::CODE_NO_LINE_AFTER_TAG,
                ClassDocSniff::CODE_MUCH_LINES_AFTER_TAG,
                ClassDocSniff::CODE_TAG_CONTENT_FORMAT_INVALID
            ]
        );
    }
}
