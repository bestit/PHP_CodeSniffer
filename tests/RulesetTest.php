<?php

declare(strict_types=1);

namespace BestIt;

use DOMDocument;
use PHPUnit\Framework\TestCase;

/**
 * Class RulesetTest.
 *
 * @author Tim Kellner <tim.kellner@bestit-online.de>
 * @package BestIt
 */
class RulesetTest extends TestCase
{
    /**
     * Test if the ruleset.xml file is valid.
     *
     * @return void
     */
    public function testRulesetXmlIsValid(): void
    {
        $xml = new DOMDocument();
        self::assertTrue($xml->load('./src/Standards/BestIt/ruleset.xml'), 'The file ruleset.xml is not valid.');
    }
}
