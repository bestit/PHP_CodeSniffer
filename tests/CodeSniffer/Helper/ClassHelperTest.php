<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHPUnit\Framework\TestCase;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;
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
    /**
     * The used file for testing.
     *
     * @var File|null
     */
    private $file;

    /**
     * This is the relevant search start for the tests.
     *
     * @var int|null
     */
    private $searchStart;

    /**
     * Sets up the test.
     *
     * @throws RuntimeException
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->file = new File(
            $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/ClassHelper/TestClass.php',
            new Ruleset($config = new Config()),
            $config
        );

        $this->file->setContent(file_get_contents($filePath));
        $this->file->parse();

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
