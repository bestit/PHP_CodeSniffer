<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHPUnit\Framework\TestCase;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Ruleset;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper as BaseHelper;
use function file_get_contents;

/**
 * Tests UseStatementHelper.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class UseStatementHelperTest extends TestCase
{
    /**
     * A specially fixed file matching the type asserts from below.
     *
     * @var File
     */
    private $file;

    /**
     * Returns values to check the type getters.
     *
     * @return array
     */
    public function getTypeAsserts(): array
    {
        return [
            UseStatement::TYPE_CONSTANT => [UseStatement::TYPE_CONSTANT, 41, 'const'],
            UseStatement::TYPE_DEFAULT => [UseStatement::TYPE_DEFAULT, 25],
            UseStatement::TYPE_FUNCTION => [UseStatement::TYPE_FUNCTION, 34, 'function']
        ];
    }

    /**
     * Sets up the test and loads the test file.
     *
     * @throws RuntimeException
     *
     * @return void
     */
    protected function setUp()
    {
        $this->file = new File(
            $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures/UseStatementHelper/FilledClass.php',
            new Ruleset($config = new Config()),
            $config
        );

        $this->file->setContent(file_get_contents($filePath));
        $this->file->parse();
    }

    /**
     * Checks if the correct value is returned.
     *
     * @dataProvider getTypeAsserts
     * @throws RuntimeException
     *
     * @param string $type
     * @param int $usePos
     *
     * @return void
     */
    public function testGetType(string $type, int $usePos)
    {
        $useStatement = new UseStatement(
            'bar',
            'foo\\bar',
            $usePos,
            $type,
            null
        );

        $this->setUp();

        static::assertSame(
            $type,
            UseStatementHelper::getType($this->file, $useStatement)
        );
    }

    /**
     * Checks if the correct value is returned.
     *
     * @dataProvider getTypeAsserts
     *
     * @param string $type
     * @param int $usePos
     * @param string $name The name which should be used in php codes for uses.
     *
     * @return void
     */
    public function testGetTypeName(string $type, int $usePos, string $name = '')
    {
        $useStatement = new UseStatement(
            'bar',
            'foo\\bar',
            $usePos,
            $type,
            null
        );

        static::assertSame(
            $name,
            UseStatementHelper::getTypeName($this->file, $useStatement)
        );
    }

    /**
     * Checks the type of the helper.
     *
     * @return void
     */
    public function testType()
    {
        $fixture = new UseStatementHelper();

        static::assertInstanceOf(BaseHelper::class, $fixture);
    }
}
