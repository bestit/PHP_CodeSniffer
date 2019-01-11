<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use function array_merge;
use function sprintf;

/**
 * Checks the sniff for the return tags.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class ReturnTagSniffTest extends AuthorTagSniffTest
{
    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_TAG_MISSING_RETURN_DESC' => ['CODE_MISSING_RETURN_DESC', 'MissingReturnDescription'],
            'CODE_TAG_MIXED_TYPE' => ['CODE_MIXED_TYPE', 'MixedType'],
        ];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fixture = new ReturnTagSniff();
    }

    /**
     * Tests description warnings after config.
     *
     * @dataProvider getCorrectFileListAsDataProvider
     *
     * @param string $file Fixture file
     *
     * @return void
     */
    public function testDescriptionWarningsWithConfig(string $file)
    {
        $unusedData = [];
        $fileMetadata = $this->getMetadataFromFilenameAsAssertArray($file, $unusedData);

        if (!$fileMetadata) {
            static::markTestSkipped(sprintf('The file %s does not contain any metadata.', basename($file)));
        }

        $callData = array_merge($fileMetadata, [['descAsWarning' => true]]);

        $this->assertWarningsInFile(...$callData);
    }
}
