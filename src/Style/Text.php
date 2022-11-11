<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Style;

use BeBat\ConsoleColor\StyleInterface;

/**
 * Text styles. Some of these may not be fully supported.
 */
enum Text: int implements StyleInterface
{
    case None            = 0;
    case Bold            = 1;
    case Faint           = 2;
    case Italic          = 3;
    case Underline       = 4;
    case Blink           = 5;
    case Reverse         = 7;
    case Concealed       = 8;
    case Strike          = 9;
    case DoubleUnderline = 21;
    case Overline        = 53;
    case Superscript     = 73;
    case Subscript       = 74;

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
