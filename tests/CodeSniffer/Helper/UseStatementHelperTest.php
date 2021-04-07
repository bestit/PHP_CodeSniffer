<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer\Helper;

use PHP_CodeSniffer\Files\File;
use PHPUnit\Framework\TestCase;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper as BaseHelper;
use function mt_rand;

/**
 * Tests UseStatementHelper.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer\Helper
 */
class UseStatementHelperTest extends TestCase
{
    /**
     * Returns values to check the type getters.
     *
     * @return array
     */
    public function getTypeAsserts(): array
    {
        return [
            UseStatement::TYPE_CONSTANT => [UseStatement::TYPE_CONSTANT, 'const'],
            UseStatement::TYPE_CLASS => [UseStatement::TYPE_CLASS],
            UseStatement::TYPE_FUNCTION => [UseStatement::TYPE_FUNCTION, 'function'],
        ];
    }

    /**
     * Checks if the correct value is returned.
     *
     * @dataProvider getTypeAsserts
     * @param string $type
     *
     * @return void
     */
    public function testGetType(string $type): void
    {
        $useStatement = new UseStatement(
            'bar',
            'foo\\bar',
            mt_rand(1, 1000),
            $type,
            null,
        );

        static::assertSame(
            $type,
            UseStatementHelper::getType($this->createMock(File::class), $useStatement),
        );
    }

    /**
     * Checks if the correct value is returned.
     *
     * @dataProvider getTypeAsserts
     *
     * @param string $type
     * @param string $name The name which should be used in php codes for uses.
     *
     * @return void
     */
    public function testGetTypeName(string $type, string $name = ''): void
    {
        $useStatement = new UseStatement(
            'bar',
            'foo\\bar',
            mt_rand(1, 1000),
            $type,
            null,
        );

        static::assertSame(
            $name,
            UseStatementHelper::getTypeName($this->createMock(File::class), $useStatement),
        );
    }

    /**
     * Checks the type of the helper.
     *
     * @return void
     */
    public function testType(): void
    {
        $testedObject = new UseStatementHelper();

        static::assertInstanceOf(BaseHelper::class, $testedObject);
    }
}
