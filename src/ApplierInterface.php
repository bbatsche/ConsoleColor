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
     *
     * @param bool|null $terminate terminate style after the text
     */
    public function apply(string $text, StyleInterface $style, ?bool $terminate = null): string;

    /**
     * Automatically terminate styles after text.
     */
    public function autoTerminate(bool $autoTerminate = true): void;

    /**
     * Ignore the support determination and always apply styles to text.
     */
    public function force(bool $force = true): void;

    /**
     * Is there a style currently applied to the output (ie: was the previously applied style not terminated)?
     */
    public function isActive(): bool;

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

    /**
     * Terminate the currently applied style.
     */
    public function terminate(): string;

    /**
     * Is the applier configured to automatically terminate styles?
     */
    public function willAutoTerminate(): bool;
}
