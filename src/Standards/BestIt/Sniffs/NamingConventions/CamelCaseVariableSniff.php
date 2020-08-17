<?php

declare(strict_types=1);

namespace BestIt\Sniffs\NamingConventions;

use BestIt\CodeSniffer\Helper\TokenHelper;
use BestIt\Sniffs\AbstractSniff;
use BestIt\Sniffs\VariableRegistrationTrait;
use PHP_CodeSniffer\Util\Common;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use function array_key_exists;
use function substr;
use const T_EQUAL;
use const T_SEMICOLON;

/**
 * Registers an error if the variables are not in camelCase (lc first).
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs\NamingConventions
 */
class CamelCaseVariableSniff extends AbstractSniff
{
    use VariableRegistrationTrait;

    /**
     * You MUST provide your vars in camel case, lower case first.
     *
     * @var string
     */
    public const CODE_NOT_CAMEL_CASE = 'NotCamelCase';

    /**
     * The error message for this sniff.
     *
     * @var string
     */
    private const MESSAGE_NOT_CAMEL_CASE = 'Variable %s should be in camelCase (lowercase first).';

    /**
     * The previously sniffed file.
     *
     * @var string
     */
    private string $prevSniffedFile = '';

    /**
     * The vars which were sniffed in this file.
     *
     * @var array
     */
    private array $sniffedVars = [];

    /**
     * Returns true if there is a value assignment or a property declaration, but which can be without an assignment.
     *
     * @return bool
     */
    protected function areRequirementsMet(): bool
    {
        $var = $this->token['content'];

        // We need to check everything != $this.
        if ($return = $var !== '$this') {
            $nextPos = (int) TokenHelper::findNextEffective($this->file, $this->stackPos + 1);

            if ($nextPos > 0) {
                $isProperty = PropertyHelper::isProperty($this->file, $this->stackPos);
                $nextTokenCode = $this->tokens[$nextPos]['code'];

                // The var should be followed by an "=" or can be followed by a semicolon if its a property.
                $return = ($nextTokenCode === T_EQUAL) || ($nextTokenCode === T_SEMICOLON && $isProperty);
            }
        }

        return $return;
    }

    /**
     * Processes the token.
     *
     * @see self::setUp()
     *
     * @return void
     */
    protected function processToken(): void
    {
        // "Sniff" the var name only once, but register the possible error everytime the var is declared.
        if (!$this->sniffedVars[$var = $this->token['content']]) {
            $this->file->recordMetric($this->stackPos, 'CamelCase var name', 'no');

            if (!$this->isSniffSuppressed(static::CODE_NOT_CAMEL_CASE)) {
                $this->file->addError(
                    self::MESSAGE_NOT_CAMEL_CASE,
                    $this->stackPos,
                    static::CODE_NOT_CAMEL_CASE,
                    [
                        $var,
                    ],
                );
            }
        } else {
            $this->file->recordMetric($this->stackPos, 'CamelCase var name', 'yes');
        }
    }

    /**
     * Sets up the sniff.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->prevSniffedFile = $this->file->getFilename();

        if (!array_key_exists($var = $this->token['content'], $this->sniffedVars)) {
            // "Sniff" the var name only once ...
            $this->sniffedVars[$var] = Common::isCamelCaps(substr($var, 1));
        }
    }

    /**
     * Cleans the cache of this sniff.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->prevSniffedFile && $this->prevSniffedFile !== $this->file->getFilename()) {
            $this->sniffedVars = [];
            $this->prevSniffedFile = '';
        }
    }
}
