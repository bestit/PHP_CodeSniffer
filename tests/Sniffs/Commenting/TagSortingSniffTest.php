<?php

declare(strict_types=1);

namespace BestIt\Sniffs\Commenting;

use BestIt\SniffTestCase;
use BestIt\Sniffs\DefaultSniffIntegrationTestTrait;
use BestIt\Sniffs\TestTokenRegistrationTrait;
use BestIt\TestRequiredConstantsTrait;
use const T_DOC_COMMENT_OPEN_TAG;

/**
 * Class TagSortingSniffTest
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\Commenting
 */
class TagSortingSniffTest extends SniffTestCase
{
    use DefaultSniffIntegrationTestTrait;
    use TestRequiredConstantsTrait;
    use TestTokenRegistrationTrait;

    /**
     * Returns the names of the required constants.
     *
     * @return array The required constants of a class. The second value is a possible value which should be checked.
     */
    public function getRequiredConstantAsserts(): array
    {
        return [
            'CODE_MISSING_NEWLINE_BETWEEN_TAGS' => ['CODE_MISSING_NEWLINE_BETWEEN_TAGS', 'MissingNewlineBetweenTags'],
            'CODE_WRONG_TAG_SORTING' => ['CODE_WRONG_TAG_SORTING', 'WrongTagSorting']
        ];
    }

    /**
     * Returns the tokens which should be checked.
     *
     * @return array Returns the expected token ids.
     */
    protected function getExpectedTokens(): array
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new TagSortingSniff();
    }
}
