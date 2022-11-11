<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Style;

use BeBat\ConsoleColor\StyleInterface;

/**
 * Additional underline styles (may not be widely supported).
 */
enum Underline: int implements StyleInterface
{
    case Single = 1;
    case Double = 2;
    case Wavy   = 3;
    case Dotted = 4;
    case Dashed = 5;

    public function ansiCode(): string
    {
        return '4:' . $this->value;
    }

    public function is256(): bool
    {
        return false;
    }

    public function isRGB(): bool
    {
        return false;
    }
}
