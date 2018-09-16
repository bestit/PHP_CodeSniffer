<?php

declare(strict_types=1);

namespace BestIt\CodeSniffer;

use Exception;

/**
 * The warning of a code does not conform to the style guide.
 *
 * We use the exception code as a string like the PDOException does, not as an integer.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\CodeSniffer
 */
class CodeWarning extends Exception
{
    /**
     * Is this topic fixable?
     *
     * @var bool
     */
    private $isFixable = false;

    /**
     * The payload for displaying the error message.
     *
     * @var array
     */
    private $payload = [];

    /**
     * The position of the error.
     *
     * @var int
     */
    private $stackPosition;

    /**
     * The erroneous token.
     *
     * @var array|void
     */
    private $token;

    /**
     * CodeWarning constructor.
     *
     * @param string $code The code for the sniffer.
     * @param string $message The message for the sniffer.
     * @param int $stackPosition Where is the token in the stack?
     */
    public function __construct(string $code, string $message, int $stackPosition)
    {
        parent::__construct($message);

        // The docs allow, that the code is not numeric (like in the PDOException) but the type hint is a number,
        // so we just use the property.
        $this->code = $code;
        $this->stackPosition = $stackPosition;
    }

    /**
     * Returns the payload for displaying the error message.
     *
     * @return array The possible payload for the error message.
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Returns the position of the error.
     *
     * @return int Where is the token in the stack?
     */
    public function getStackPosition(): int
    {
        return $this->stackPosition;
    }

    /**
     * Returns the erroneous token.
     *
     * @return array The "broken" token.
     */
    public function getToken(): array
    {
        return $this->token;
    }

    /**
     * Is this topic fixable?
     *
     * @param bool|null $newStatus If given the new status.
     *
     * @return bool Returns the "old" status if this is fixable.
     */
    public function isFixable(?bool $newStatus = null): bool
    {
        $oldStatus = $this->isFixable;

        if ($newStatus !== null) {
            $this->isFixable = $newStatus;
        }

        return $oldStatus;
    }

    /**
     * Sets the payload for displaying the error message.
     *
     * @param array $payload The possible payload for the error message.
     *
     * @return $this Fluent-Interface.
     */
    public function setPayload(array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Sets the erroneous token.
     *
     * @param array $token The "broken" token.
     *
     * @return $this Fluent Interface.
     */
    public function setToken(array $token): self
    {
        $this->token = $token;

        return $this;
    }
}
