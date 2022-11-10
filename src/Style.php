<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor;

use BeBat\ConsoleColor\Style\StyleInterface;

/**
 * Apply console colors & styles to text.
 */
final class Style
{
    public readonly bool $supports256Colors;
    public readonly bool $supportsRGBColors;
    public readonly bool $supportsStyles;
    private bool $forcedOutput = false;

    /**
     * @param resource $resource
     */
    public function __construct($resource = STDOUT)
    {
        $this->checkSupport($resource);
    }

    /**
     * Apply a style to text.
     */
    public function apply(string $text, StyleInterface $style): string
    {
        if (!$this->isForced() && (
            !$this->supportsStyles
            || ($style->is256() && !$this->supports256Colors)
            || ($style->isRGB() && !$this->supportsRGBColors)
        )) {
            return $text;
        }

        return $this->escSequence($style->ansiCode()) . $text . $this->escSequence(Style\Text::None->ansiCode());
    }

    /**
     * Apply the escape sequence to an ANSI code.
     */
    public function escSequence(string $ansiCode): string
    {
        return sprintf("\033[%sm", $ansiCode);
    }

    /**
     * Ignore the support determination and always apply styles to text.
     */
    public function force(bool $force = true): void
    {
        $this->forcedOutput = $force;
    }

    /**
     * Will styles be applied to text, regardless of whether they are supported?
     */
    public function isForced(): bool
    {
        return $this->forcedOutput;
    }

    /**
     * Does the default output (STDOUT) support styling?
     *
     * @param resource $resource
     */
    private function checkSupport($resource): void
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
            || stream_isatty($resource)
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
}
