<?php

declare(strict_types=1);

namespace BestIt\Sniffs\DocTags;

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
            'CODE_TAG_MISSING_RETURN_DESC' => ['CODE_TAG_MISSING_RETURN_DESC', 'MissingReturnDescription'],
            'CODE_TAG_MIXED_TYPE' => ['CODE_TAG_MIXED_TYPE', 'MixedType'],
            'CODE_TAG_NOT_EQUAL_TO_RETURN_TYPE' => ['CODE_TAG_NOT_EQUAL_TO_RETURN_TYPE', 'NotEqualToReturnType'],
            'CODE_NO_ARRAY_FOUND' => ['CODE_NO_ARRAY_FOUND', 'NoArrayFound'],
            'CODE_NULLABLE_RETURN_FOUND' => ['CODE_NULLABLE_RETURN_FOUND', 'NullableReturnFound']
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

        $this->fixture = new ReturnTagSniff();
    }
}
