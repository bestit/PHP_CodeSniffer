<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use SlevomatCodingStandard\Helpers\ClassHelper as BaseHelper;

/**
 * Tests ClassHelper.
 *
 * @author b3nl <code@b3nl.de>
 * @package BestIt\CodeSniffer\Helper
 */
class ClassHelperTest extends TestCase
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
        $this->file = $this->getFile(__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/ClassHelper/TestClass.php');

        $this->searchStart = 44;
    }

    /**
     * Checks if the pointers are returned.
     *
     * @return void
     */
    public function testGetTraitUsePointers(): void
    {
        static::assertSame([51, 57], ClassHelper::getTraitUsePointers($this->file, 44));
    }

    /**
     * Checks the type.
     *
     * @return void
     */
    public function testType(): void
    {
        static::assertInstanceOf(BaseHelper::class, new ClassHelper());
    }
}
