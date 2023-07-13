<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Style;

use BeBat\ConsoleColor\StyleInterface;
use DomainException;

/**
 * Any color from the 256 color palette.
 */
final class Color256 implements StyleInterface
{
    private const FOREGROUND = 38;
    private const BACKGROUND = 48;
    private const UNDERLINE  = 58;

    private function __construct(
        private int $code,
        private int $placement,
    ) {
        if (!(0 <= $code && $code <= 255)) {
            throw new DomainException('Color value must be from 0 to 255.');
        }
    }

    /**
     * Get a 256 color for the background.
     *
     * @throws DomainException if $code is outside 0 - 255
     */
    public static function background(int $code): self
    {
        return new self($code, self::BACKGROUND);
    }

    /**
     * Get a 256 color for the foreground.
     *
     * @throws DomainException if $code is outside 0 - 255
     */
    public static function foreground(int $code): self
    {
        return new self($code, self::FOREGROUND);
    }

    /**
     * Apply color to just the text underline (may not be widely supported).
     *
     * @throws DomainException if $code is outside 0 - 255
     */
    public static function underline(int $code): self
    {
        return new self($code, self::UNDERLINE);
    }

    public function ansiCode(): string
    {
        return sprintf('%d:5:%d', $this->placement, $this->code);
    }

    public function is256(): bool
    {
        return true;
    }

    public function isRGB(): bool
    {
        return false;
    }
}
