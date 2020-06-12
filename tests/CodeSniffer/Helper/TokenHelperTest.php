<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHPUnit\Framework\TestCase;
use SlevomatCodingStandard\Helpers\TokenHelper as BaseHelper;
use const DIRECTORY_SEPARATOR;
use const T_DOC_COMMENT_TAG;
use const T_IF;

/**
 * Class TokenHelperTest.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class TokenHelperTest extends TestCase
{
    use FileHelperTrait;

    /**
     * This is the relevant search start for the tests.
     *
     * @var int|null
     */
    private ?int $searchStart = null;

    /**
     * Sets up the test.
     *
     * @throws RuntimeException
     *
     * @return void
     */
    protected function setUp(): void
    {
        $file = $this->getFile(__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/TokenHelper/phpdoc.php');

        $this->file = $file;

        $this->searchStart = 16;
    }

    /**
     * The doc comment should not contain any ifs!
     *
     * @return void
     */
    public function testFindNextAllNone(): void
    {
        static::assertSame([], TokenHelper::findNextAll($this->file, [T_IF], $this->searchStart));
    }

    /**
     * Only one tag should be returned, because we stop right after the first one.
     *
     * @return void
     */
    public function testFindNextAllPrematureEnd(): void
    {
        static::assertSame([38], TokenHelper::findNextAll($this->file, [T_DOC_COMMENT_TAG], $this->searchStart, 39));
    }

    /**
     * Checks if all pointers are returned.
     *
     * @return void
     */
    public function testFindNextAllSuccess(): void
    {
        static::assertSame([38, 45], TokenHelper::findNextAll($this->file, [T_DOC_COMMENT_TAG], $this->searchStart));
    }

    /**
     * Checks the handling if the prev content is not found.
     *
     * @return void
     */
    public function testFindPreviousContentNone(): void
    {
        static::assertNull(
            TokenHelper::findPreviousContent($this->file, [T_DOC_COMMENT_TAG], '@foobar', 45)
        );
    }

    /**
     * Checks if the prev content is loaded.
     *
     * @return void
     */
    public function testFindPreviousContentSuccess(): void
    {
        static::assertSame(
            38,
            TokenHelper::findPreviousContent($this->file, [T_DOC_COMMENT_TAG], '@author', 45)
        );
    }

    /**
     * Checks if the helper is of the correct instance.
     *
     * @return void
     */
    public function testType(): void
    {
        static::assertInstanceOf(BaseHelper::class, new TokenHelper());
    }
}
