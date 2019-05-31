<?php

declare(strict_types=1);

namespace BestIt\Sniffs;

use BestIt\CodeSniffer\File;
use SlevomatCodingStandard\Helpers\SuppressHelper;

/**
 * Helps you check if a sniff is suppressed.
 *
 * @author blange <bjoern.lange@bestit-online.de>
 * @package BestIt\Sniffs
 */
trait SuppressingTrait
{
    /**
     * The used suppresshelper.
     *
     * Filled by the lazy load getter.
     *
     * @var SuppressHelper|null
     */
    private $suppressHelper = null;

    /**
     * Type-safe getter for the file.
     *
     * @return File
     */
    abstract protected function getFile(): File;

    /**
     * Get the sniff name.
     *
     * @param string|null $sniffName If there is an optional sniff name.
     *
     * @return string Returns the special sniff name in the code sniffer context.
     */
    private function getSniffName(?string $sniffName = null): string
    {
        $sniffClassName = preg_replace(
            '/Sniff$/',
            '',
            str_replace(['\\', '.Sniffs'], ['.', ''], static::class)
        );

        if ($sniffName) {
            $sniffClassName .= '.' . $sniffName;
        }

        return $sniffClassName;
    }

    /**
     * Type-safe getter for the stack position.
     *
     * @return int
     */
    abstract protected function getStackPos(): int;

    /**
     * Returns the used suppress helper.
     *
     * @return SuppressHelper The suppress helper.
     */
    private function getSuppressHelper(): SuppressHelper
    {
        if (!$this->suppressHelper) {
            $this->suppressHelper = new SuppressHelper();
        }

        return $this->suppressHelper;
    }

    /**
     * Returns true if this sniff or a rule of this sniff is suppressed with the slevomat suppress annotation.
     *
     * @param null|string $rule The optional rule.
     * @param int|null $stackPos Do you want ot overload the position for the which position the sniff is suppressed.
     *
     * @return bool Returns true if the sniff is suppressed.
     */
    protected function isSniffSuppressed(?string $rule = null, ?int $stackPos = null): bool
    {
        return $this->getSuppressHelper()->isSniffSuppressed(
            $this->getFile(),
            $stackPos ?? $this->getStackPos(),
            $this->getSniffName($rule)
        );
    }
}
