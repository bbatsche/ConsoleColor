<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor;

use const PHP_OS_FAMILY;

final class Style implements ApplierInterface
{
    public readonly bool $supports256Colors;
    public readonly bool $supportsRGBColors;
    public readonly bool $supportsStyles;
    private bool $active        = false;
    private bool $autoTerminate = true;
    private bool $forcedOutput  = false;

    /**
     * @param resource $resource
     */
    public function __construct($resource = \STDOUT)
    {
        if (!\in_array(\PHP_SAPI, ['cli', 'cli-server', 'phpdbg'], true)
            || getenv('NO_COLOR') !== false
        ) {
            $this->supportsStyles    = false;
            $this->supports256Colors = false;
            $this->supportsRGBColors = false;

            return;
        }

        if ((PHP_OS_FAMILY === 'Windows' && sapi_windows_vt100_support($resource))
            || stream_isatty($resource) || getenv('FORCE_COLOR') !== false
        ) {
            $this->supportsStyles = true;

            if ((PHP_OS_FAMILY === 'Windows'
                && (getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON'))
                || getenv('COLORTERM') === 'truecolor'
            ) {
                $this->supports256Colors = true;
                $this->supportsRGBColors = true;
            } elseif (str_contains((string) getenv('TERM'), '256color')) {
                $this->supports256Colors = true;
                $this->supportsRGBColors = false;
            } else {
                $this->supports256Colors = false;
                $this->supportsRGBColors = false;
            }
        } else {
            $this->supportsStyles    = false;
            $this->supports256Colors = false;
            $this->supportsRGBColors = false;
        }
    }

    public function autoTerminate(bool $autoTerminate = true): void
    {
        $this->autoTerminate = $autoTerminate;
    }

    public function apply(string $text, StyleInterface $style, ?bool $terminate = null): string
    {
        if (!$this->isForced() && (
            !$this->supportsStyles
            || ($style->is256() && !$this->supports256Colors)
            || ($style->isRGB() && !$this->supportsRGBColors)
        )) {
            return $text;
        }

        $tail = ($terminate === true || ($terminate === null && $this->willAutoTerminate()))
            ? $this->terminator()
            : '';

        $this->active = $tail === '' && !str_ends_with($style->ansiCode(), Style\Text::None->ansiCode());

        return $this->escSequence($style->ansiCode()) . $text . $tail;
    }

    /**
     * Apply the escape sequence to an ANSI code.
     */
    public function escSequence(string $ansiCode): string
    {
        return \sprintf("\e[%sm", $ansiCode);
    }

    public function force(bool $force = true): void
    {
        $this->forcedOutput = $force;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isForced(): bool
    {
        return $this->forcedOutput;
    }

    public function supportsStyles(): bool
    {
        return $this->supportsStyles;
    }

    public function supports256Colors(): bool
    {
        return $this->supports256Colors;
    }

    public function supportsRGBColors(): bool
    {
        return $this->supportsRGBColors;
    }

    public function terminate(): string
    {
        $this->active = false;

        return $this->supportsStyles() || $this->isForced()
            ? $this->terminator()
            : '';
    }

    public function willAutoTerminate(): bool
    {
        return $this->autoTerminate;
    }

    /**
     * Get style terminator sequence.
     */
    private function terminator(): string
    {
        return $this->escSequence(Style\Text::None->ansiCode());
    }
}
