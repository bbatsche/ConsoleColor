#!/usr/bin/env php
<?php

declare(strict_types=1);

use BeBat\ConsoleColor\Style;

use const PHP_EOL;
use const STR_PAD_LEFT;

$localPath     = __DIR__ . '/vendor/autoload.php';
$installedPath = dirname(__DIR__, 2) . '/autoload.php';

if (is_file($installedPath)) {
    require_once $installedPath;
} elseif (is_file($localPath)) {
    require_once $localPath;
} else {
    throw new RuntimeException('Could not find autoload file. Make sure to run "composer install" first.');
}

$style = new Style();
$style->force();

echo 'Styling supported: ' . ($style->supportsStyles ? 'Yes' : 'No') . PHP_EOL;
echo '256 colors supported: ' . ($style->supports256Colors ? 'Yes' : 'No') . PHP_EOL;
echo '24-bit true colors supported: ' . ($style->supportsRGBColors ? 'Yes' : 'No') . PHP_EOL;

echo PHP_EOL;

function printEnumStyleSample(string $heading, string $enum): void
{
    global $style;

    echo $heading . PHP_EOL;

    foreach ($enum::cases() as $styleValue) {
        echo '  ' . $style->apply($styleValue->name, $styleValue) . PHP_EOL;
    }

    echo PHP_EOL;
}

function print256ColorSample(string $heading, string $method): void
{
    global $style;

    echo "256 {$heading} Colors" . PHP_EOL;

    for ($i = 0; $i < 256; ++$i) {
        if ($i === 0 || $i < 231 && $i % 18 === 16 || 231 < $i && $i % 12 === 4) {
            echo '  ';
        }

        echo $style->apply(
            str_pad((string) $i, 4, ' ', STR_PAD_LEFT),
            Style\Color256::{$method}($i),
        );

        if ($i === 15 || $i === 231) {
            echo PHP_EOL . PHP_EOL;
        }
        if (15 < $i && $i < 231 && $i % 18 === 15 || $i === 243 || $i === 255) {
            echo PHP_EOL;
        }
    }

    echo PHP_EOL;
}

function printTrueColorSample(string $heading, string $method): void
{
    global $style;

    echo "True Color {$heading} (Abridged)" . PHP_EOL;

    for ($red = 0; $red < 8; ++$red) {
        for ($green = 0; $green < 8; ++$green) {
            for ($blue = 0; $blue < 8; ++$blue) {
                if ($blue % 8 === 0 && $green % 8 === 0) {
                    echo '  ';
                }

                echo $style->apply(
                    'X',
                    Style\ColorRGB::{$method}($red * 32, $green * 32, $blue * 32),
                );

                if ($blue % 8 === 7 && $green % 8 === 7) {
                    echo PHP_EOL;
                }
            }
        }
    }

    echo PHP_EOL;
}

printEnumStyleSample('Text Styles', Style\Text::class);
printEnumStyleSample('Underline Styles', Style\Underline::class);
printEnumStyleSample('Foreground Colors', Style\Color::class);
printEnumStyleSample('Background Colors', Style\BackgroundColor::class);

print256ColorSample('Foreground', 'foreground');
print256ColorSample('Background', 'background');

echo '256 Underline Colors' . PHP_EOL;

for ($i = 0; $i < 256; ++$i) {
    if ($i === 0 || $i < 231 && $i % 18 === 16 || 231 < $i && $i % 12 === 4) {
        echo '  ';
    }

    echo $style->apply(
        str_pad((string) $i, 4, ' ', STR_PAD_LEFT),
        new Style\Composite(Style\Text::Underline, Style\Color256::underline($i)),
    );

    if ($i === 15 || $i === 231) {
        echo PHP_EOL . PHP_EOL;
    }
    if (15 < $i && $i < 231 && $i % 18 === 15 || $i === 243 || $i === 255) {
        echo PHP_EOL;
    }
}

echo PHP_EOL;

printTrueColorSample('Foreground', 'foreground');
printTrueColorSample('Background', 'background');

echo 'True Color Underline (Abridged)' . PHP_EOL;

for ($red = 0; $red < 8; ++$red) {
    for ($green = 0; $green < 8; ++$green) {
        for ($blue = 0; $blue < 8; ++$blue) {
            if ($blue % 8 === 0 && $green % 8 === 0) {
                echo '  ';
            }

            echo $style->apply(
                'X',
                new Style\Composite(
                    Style\Text::Underline,
                    Style\ColorRGB::underline($red * 32, $green * 32, $blue * 32),
                ),
            );

            if ($blue % 8 === 7 && $green % 8 === 7) {
                echo PHP_EOL;
            }
        }
    }
}

echo PHP_EOL;
