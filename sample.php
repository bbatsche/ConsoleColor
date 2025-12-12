#!/usr/bin/env php
<?php

declare(strict_types=1);

use BeBat\ConsoleColor\ApplierInterface;
use BeBat\ConsoleColor\Style;
use BeBat\ConsoleColor\StyleInterface;

$localPath     = __DIR__ . '/vendor/autoload.php';
$installedPath = dirname(__DIR__, 2) . '/autoload.php';

if (is_file($installedPath)) {
    require_once $installedPath;
} elseif (is_file($localPath)) {
    require_once $localPath;
} else {
    throw new RuntimeException('Could not find autoload file. Make sure to run "composer install" first.');
}

final class StyleDemo
{
    private const INDENT = '  ';

    public function __construct(private ApplierInterface $applier) {}

    /**
     * @param class-string<StyleInterface&UnitEnum> $enum
     */
    public function enumStyle(string $enum): string
    {
        return array_reduce(
            $enum::cases(),
            fn (string $result, StyleInterface|UnitEnum $style): string => $result . self::INDENT . $this->applier->apply($style->name, $style) . \PHP_EOL,
            '',
        );
    }

    /**
     * @param 'background'|'foreground'|'underline' $method
     */
    public function style256Color(string $method): string
    {
        $result = '';

        $this->applier->autoTerminate(false);

        for ($i = 0; $i < 256; ++$i) {
            $style = Style\Color256::{$method}($i);

            if ($method === 'underline') {
                $style = new Style\Composite(Style\Text::Underline, $style);
            }

            if ($i === 0 || $i < 231 && $i % 18 === 16 || 231 < $i && $i % 12 === 4) {
                $result .= self::INDENT;
            }

            $result .= $this->applier->apply(
                str_pad((string) $i, 4, ' ', \STR_PAD_LEFT),
                $style,
            );

            if ($i === 15 || $i === 231) {
                $result .= $this->applier->terminate() . \PHP_EOL . \PHP_EOL;
            }
            if (15 < $i && $i < 231 && $i % 18 === 15 || $i === 243 || $i === 255) {
                $result .= $this->applier->terminate() . \PHP_EOL;
            }
        }

        $this->applier->autoTerminate();

        return $result;
    }

    /**
     * @param 'background'|'foreground'|'underline' $method
     */
    public function styleTrueColor(string $method): string
    {
        $result = '';

        $this->applier->autoTerminate(false);

        for ($red = 0; $red < 8; ++$red) {
            for ($green = 0; $green < 8; ++$green) {
                for ($blue = 0; $blue < 8; ++$blue) {
                    $style = Style\ColorRGB::{$method}($red * 32, $green * 32, $blue * 32);

                    if ($method === 'underline') {
                        $style = new Style\Composite(Style\Text::Underline, $style);
                    }

                    if ($blue % 8 === 0 && $green % 8 === 0) {
                        $result .= self::INDENT;
                    }

                    $result .= $this->applier->apply('X', $style);

                    if ($blue % 8 === 7 && $green % 8 === 7) {
                        $result .= $this->applier->terminate() . \PHP_EOL;
                    }
                }
            }
        }

        $this->applier->autoTerminate();

        return $result;
    }
}

$style = new Style();
$style->force();
$demo = new StyleDemo($style);

echo 'Styling supported: ' . ($style->supportsStyles ? 'Yes' : 'No') . \PHP_EOL;
echo '256 colors supported: ' . ($style->supports256Colors ? 'Yes' : 'No') . \PHP_EOL;
echo '24-bit true colors supported: ' . ($style->supportsRGBColors ? 'Yes' : 'No') . \PHP_EOL;

echo \PHP_EOL;

echo 'Text Styles' . \PHP_EOL;
echo $demo->enumStyle(Style\Text::class) . \PHP_EOL;
echo 'Underline Styles' . \PHP_EOL;
echo $demo->enumStyle(Style\Underline::class) . \PHP_EOL;
echo 'Foreground Colors' . \PHP_EOL;
echo $demo->enumStyle(Style\Color::class) . \PHP_EOL;
echo 'Background Colors' . \PHP_EOL;
echo $demo->enumStyle(Style\BackgroundColor::class) . \PHP_EOL;

echo \PHP_EOL . '256 Foreground Colors' . \PHP_EOL;
echo $demo->style256Color('foreground');
echo \PHP_EOL . '256 Background Colors' . \PHP_EOL;
echo $demo->style256Color('background');
echo \PHP_EOL . '256 Underline Colors' . \PHP_EOL;
echo $demo->style256Color('underline');

echo \PHP_EOL . 'True Color Foreground (Abridged)' . \PHP_EOL;
echo $demo->styleTrueColor('foreground');
echo \PHP_EOL . 'True Color Background (Abridged)' . \PHP_EOL;
echo $demo->styleTrueColor('background');
echo \PHP_EOL . 'True Color Underline (Abridged)' . \PHP_EOL;
echo $demo->styleTrueColor('underline');
