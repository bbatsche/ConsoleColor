<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Style;

use BeBat\ConsoleColor\StyleInterface;
use DomainException;

/**
 * Any color from the true color palette.
 */
final class ColorRGB implements StyleInterface
{
    private const FOREGROUND = 38;
    private const BACKGROUND = 48;
    private const UNDERLINE  = 58;

    private function __construct(
        private int $red,
        private int $green,
        private int $blue,
        private int $placement,
    ) {
        foreach ([$red, $green, $blue] as $code) {
            if (!(0 <= $code && $code <= 255)) {
                throw new DomainException('Color value must be from 0 to 255.');
            }
        }
    }

    /**
     * Get a RGB color for the background.
     *
     * @throws DomainException if a color is outside 0 - 255
     */
    public static function background(int $red, int $green, int $blue): self
    {
        return new self($red, $green, $blue, self::BACKGROUND);
    }

    /**
     * Get a RGB color for the foreground.
     *
     * @throws DomainException if a color is outside 0 - 255
     */
    public static function foreground(int $red, int $green, int $blue): self
    {
        return new self($red, $green, $blue, self::FOREGROUND);
    }

    /**
     * Apply color to just the text underline (may not be widely supported).
     *
     * @throws DomainException if a color is outside 0 - 255
     */
    public static function underline(int $red, int $green, int $blue): self
    {
        return new self($red, $green, $blue, self::UNDERLINE);
    }

    public function ansiCode(): string
    {
        return sprintf('%d:2:%d:%d:%d', $this->placement, $this->red, $this->green, $this->blue);
    }

    public function is256(): bool
    {
        return true;
    }

    public function isRGB(): bool
    {
        return true;
    }
}
