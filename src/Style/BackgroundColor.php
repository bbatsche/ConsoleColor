<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Style;

/**
 * Basic background colors.
 */
enum BackgroundColor: int implements StyleInterface
{
    case Black         = 40;
    case Red           = 41;
    case Green         = 42;
    case Yellow        = 43;
    case Blue          = 44;
    case Magenta       = 45;
    case Cyan          = 46;
    case White         = 47;
    case Default       = 49;
    case BrightBlack   = 100;
    case BrightRed     = 101;
    case BrightGreen   = 102;
    case BrightYellow  = 103;
    case BrightBlue    = 104;
    case BrightMagenta = 105;
    case BrightCyan    = 106;
    case BrightWhite   = 107;

    public function ansiCode(): string
    {
        return (string) $this->value;
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
