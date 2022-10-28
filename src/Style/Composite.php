<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Style;

/**
 * Apply more than one style to text simultaneously.
 */
final class Composite implements StyleInterface
{
    /** @var StyleInterface[] */
    private array $styles;

    public function __construct(StyleInterface $style, StyleInterface ...$styles)
    {
        array_unshift($styles, $style);

        $this->styles = $styles;
    }

    public function ansiCode(): string
    {
        return implode(';', array_map(static fn (StyleInterface $style) => $style->ansiCode(), $this->styles));
    }

    public function is256(): bool
    {
        foreach ($this->styles as $style) {
            if ($style->is256()) {
                return true;
            }
        }

        return false;
    }

    public function isRGB(): bool
    {
        foreach ($this->styles as $style) {
            if ($style->isRGB()) {
                return true;
            }
        }

        return false;
    }
}
