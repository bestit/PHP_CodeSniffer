<?php

use Doctrine\Instantiator\Instantiator;
use PHPUnit\Framework\Error,
    Name\Space;
use PHPUnit\Exception;

/**
 * Class Import.
 * @author Mika Bertels <mika.bertels@bestit-online.de>
 * @package Test\Package
 */
class Import
{
    /**
     * Test method.
     *
     * @param Instantiator $instantiator
     *
     * @return bool
     */
    public function testMethod(Instantiator $instantiator): bool
    {
        return true;
    }

    /**
     * Test method.
     *
     * @param Error $error
     *
     * @return bool
     */
    public function secondndTestMethod(PHPUnit\Framework\Error $error): bool
    {
        return true;
    }
}
