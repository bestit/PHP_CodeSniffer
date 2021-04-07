<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\SniffTestCase;
use BestIt\TestRequiredConstantsTrait;
use function array_merge;
use function sprintf;
use const T_DOC_COMMENT_TAG;

/**
 * Class ParamTagSniffTest
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\DocTags
 */
class ParamTagSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Returns the tokens which should be checked.
     *
     * @return array Returns the expected token ids.
     */
    protected function getExpectedTokens(): array
    {
        return [T_DOC_COMMENT_TAG];
    }

    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_TAG_MISSING_DESC' => ['CODE_TAG_MISSING_DESC', 'MissingDesc'],
            'CODE_TAG_MISSING_VARIABLES' => ['CODE_TAG_MISSING_VARIABLES', 'MissingVariables'],
            'CODE_TAG_MISSING_VARIABLE' => ['CODE_TAG_MISSING_VARIABLE', 'MissingVariable'],
            'CODE_TAG_MISSING_TYPE' => ['CODE_TAG_MISSING_TYPE', 'MissingType'],
            'CODE_TAG_MIXED_TYPE' => ['CODE_TAG_MIXED_TYPE', 'MixedType'],
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

        $this->testedObject = new ParamTagSniff();

        $this->testedObject->descAsWarning = true;
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
    public function testDescriptionWarningsWithConfig(string $file): void
    {
        $unusedData = [];
        $fileMetadata = $this->getMetadataFromFilenameAsAssertArray($file, $unusedData);

        if (!$fileMetadata) {
            static::markTestSkipped(sprintf('The file %s does not contain any metadata.', basename($file)));
        }

        $callData = array_merge($fileMetadata, [['descAsWarning' => true]]);

        $this->assertWarningsInFile(...$callData);
    }

    /**
     * Checks the basic type of the sniff.
     *
     * @return void
     */
    public function testType(): void
    {
        static::assertInstanceOf(AbstractTagSniff::class, $this->testedObject);
    }
}
