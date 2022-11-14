<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor;

/**
 * Apply console colors & styles to text.
 */
interface ApplierInterface
{
    /**
     * Apply a style to text.
     */
    public function apply(string $text, StyleInterface $style): string;

    /**
     * Ignore the support determination and always apply styles to text.
     */
    public function force(bool $force = true): void;

    /**
     * Will styles be applied to text, regardless of whether they are supported?
     */
    public function isForced(): bool;

    /**
     * Does the output stream support styles?
     */
    public function supportsStyles(): bool;

    /**
     * Does the output stream support 256 colors?
     */
    public function supports256Colors(): bool;

    /**
     * Does the output stream support 24-bit or RGB "true" colors?
     */
    public function supportsRGBColors(): bool;
}
