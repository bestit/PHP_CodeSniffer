<?php

declare(strict_types=1);

namespace Tests\BestIt\Sniffs;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Sniffs\TestCase as SlevomatTestCase;

/**
 * Class TestCase
 *
 * @package Tests\BestIt\Sniffs
 *
 * @author Nick Lubisch <nick.lubisch@bestit-online.de>
 */
abstract class TestCase extends SlevomatTestCase
{
    /**
     * @inheritdoc
     */
    protected function getSniffClassName(): string
    {
        $className = get_class($this);

        $className = str_replace('Tests\\', '', $className);

        return substr($className, 0, -strlen('Test'));
    }

    /**
     * @inheritdoc
     */
    protected function assertAllFixedInFile(PHP_CodeSniffer_File $codeSnifferFile)
    {
        $codeSnifferFile->fixer->fixFile();

        $this->assertStringEqualsFile(
            preg_replace('~(\\.php)$~', '.Fixed\\1', $codeSnifferFile->getFilename()),
            $codeSnifferFile->fixer->getContents()
        );
    }
}
