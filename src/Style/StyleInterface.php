<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Style;

/**
 * General style features.
 */
interface StyleInterface
{
    /**
     * Get the ANSI code for a style.
     */
    public function ansiCode(): string;

    /**
     * Does this style require 256 color support?
     */
    public function is256(): bool;

    /**
     * Does this style require 24-bit (true) color support?
     */
    public function isRGB(): bool;
}
