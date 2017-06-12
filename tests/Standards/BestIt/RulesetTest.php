<?php

declare(strict_types=1);

namespace Tests\BestIt;

use DOMDocument;
use PHPUnit\Framework\TestCase;

/**
 * Class RulesetTest.
 *
 * @package Tests\BestIt
 * @author Tim Kellner <tim.kellner@bestit-online.de>
 */
class RulesetTest extends TestCase
{
    /**
     * Test if the ruleset.xml file is valid.
     */
    public function testRulesetXmlIsValid()
    {
        $xml = new DOMDocument();
        self::assertTrue($xml->load('./src/Standards/BestIt/ruleset.xml'), 'The file ruleset.xml is not valid.');
    }
}
