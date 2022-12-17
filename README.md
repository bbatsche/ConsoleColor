# Console Color

[![Latest Stable Version](https://img.shields.io/packagist/v/bebat/console-color.svg?style=flat-square)](https://packagist.org/packages/bebat/console-color)
[![Required PHP Version](https://img.shields.io/packagist/php-v/bebat/console-color.svg?style=flat-square)](https://packagist.org/packages/bebat/console-color)
[![License](https://img.shields.io/packagist/l/bebat/console-color?style=flat-square)](LICENSE)
[![Acceptance Test Status](https://img.shields.io/github/actions/workflow/status/bbatsche/ConsoleColor/acceptance.yml?branch=develop&style=flat-square)](https://github.com/bbatsche/ConsoleColor/actions/workflows/acceptance.yml)
[![Code Coverage](https://img.shields.io/codecov/c/github/bbatsche/ConsoleColor?style=flat-square)](https://codecov.io/gh/bbatsche/ConsoleColor)

Console Color is a lightweight PHP 8.1+ library for adding color & other styles to text in the command line.

- [Installation](#installation)
- [Basic Usage](#basic-usage)
  - [Environment Variables](#environment-variables)
- [Included Styles](#included-styles)
  - [Basic Styles](#basic-styles)
    - [Text](#text)
    - [Underline](#underline)
    - [Foreground & Background Color](#foreground--background-color)
  - [256 & True Color](#256--true-color)
  - [Composite Styles](#composite-styles)

## Installation

The Console Color library can be installed via [Composer](https://getcomposer.org/):

```bash
composer require bebat/console-color
```

## Basic Usage

To apply style to a text you need an instance of `BeBat\ConsoleColor\Style`. With that you can _apply_ a style object to some text:

```php
use BeBat\ConsoleColor\Style;

$style = new Style();

echo $style->apply('This text is green', Style\Color::Green) . PHP_EOL;
```

The `Style` class will try to determine if console output can be styled automatically (more details on this can be found in the Environment Variables section [below](#environment-variables)). By default, it looks at `STDOUT` and checks if it is a TTY. If you are using a different output type (such as a `php://` I/O stream) you may pass that to `Style()` to have it determine if that stream supports styling:

```php
use BeBat\ConsoleColor\Style;

$handle = fopen($something, 'w');
$style = new Style($handle);

fwrite($handle, $style->apply(
    'If $something points to a console this text will have a cyan background. ' .
    'Otherwise it will just be plain.',
    Style\BackgroundColor::Cyan,
));
```

You may use the `force()` method to make `Style` _always_ apply styles to text, regardless of whether it thinks the output resource supports it:

```php
use BeBat\ConsoleColor\Style;

$handle = fopen($something, 'w');
$style = new Style($handle);
$style->force();

fwrite($handle, $style->apply(
    'This text will always have styling applied to it.', Style\Text::Bold,
));
```

### Environment Variables

In addition to checking if `STDOUT` is a TTY, `Style` will look at several environment variables to determine if styles should be applied, and what level of support the user's terminal has for styles.

* `FORCE_COLOR` - If the user has set `FORCE_COLOR` styling will be applied, even if `Style` doesn't think the output is a TTY.
* `NO_COLOR` - If the user has set `NO_COLOR` styling will be disabled. `NO_COLOR` takes precedence over `FORCE_COLOR`.
* `TERM` - `Style` will check `TERM` to see if it supports 256 colors.
* `COLORTERM` - If `COLORTERM` is set to `truecolor` then `Style` will apply RGB based colors.

## Included Styles

`Style::apply()` accepts an instance of [`StyleInterface`](src/StyleInterface.php), and Console Color includes a number of styles that implement this interface.

To see what styles your terminal supports and what they look like, run the included `sample.php` file.

### Basic Styles

`Style\Text`, `Style\Underline`, `Style\Color`, and `Style\BackgroundColor` are [backed enumerations](https://www.php.net/manual/en/language.enumerations.backed.php) which helps to make Console Color's API extremely clean. These styles have the broadest support in most terminals (with some exceptions).

#### Text

* `Style\Text::None` - no style
* `Style\Text::Bold` - bold text (may also "brighten" the color in some terminals)
* `Style\Text::Faint` - dim text color
* `Style\Text::Italic` - italics
* `Style\Text::Underline` - underline
* `Style\Text::Blink` - blinking text (use wisely 😉)
* `Style\Text::Reverse` - swap foreground and background colors
* `Style\Text::Concealed` - hides the text (it is still present and can be selected)
* `Style\Text::Strike` - strikethrough, not supported in all terminals
* `Style\Text::DoubleUnderline` - double or bold underline, not supported in all terminals
* `Style\Text::Overline` - line over text, not supported in all terminals
* `Style\Text::Superscript` - superscript text, rarely supported
* `Style\Text::Subscript` - subscript text, rarely supported

#### Underline

Custom underline styles are not widely supported but some terminals do include them. Most, if not all, will fall back on just displaying a single underline.

* `Style\Underline::Single`
* `Style\Underline::Double`
* `Style\Underline::Wavy`
* `Style\Underline::Dotted`
* `Style\Underline::Dashed`

#### Foreground & Background Color

`Style\Color` and `Style\BackgroundColor` both support the same values and have an identical API.

* `Style\Color::Black`
* `Style\Color::Red`
* `Style\Color::Green`
* `Style\Color::Yellow`
* `Style\Color::Blue`
* `Style\Color::Magenta`
* `Style\Color::Cyan`
* `Style\Color::White`
* `Style\Color::Default`
* `Style\Color::BrightBlack`
* `Style\Color::BrightRed`
* `Style\Color::BrightGreen`
* `Style\Color::BrightYellow`
* `Style\Color::BrightBlue`
* `Style\Color::BrightMagenta`
* `Style\Color::BrightCyan`
* `Style\Color::BrightWhite`

### 256 & True Color

Most terminal programs support 256 color output, and many now also support 24-bit "true" colors. Console Color can apply those colors to the foreground, background, or underline color through the `Style\Color256` and `Style\ColorRGB` classes.

* `Style\Color256::foreground(int $code)` - apply a 256 color to the text
* `Style\Color256::background(int $code)` - apply a 256 color to the background
* `Style\Color256::underline(int $code)` - apply a 256 color to an underline (see composite styles [below](#composite-styles))
* `Style\ColorRGB::foreground(int $red, int $green, int $blue)` - apply an RGB color to the text
* `Style\ColorRGB::background(int $red, int $green, int $blue)` - apply an RGB color to the background
* `Style\ColorRGB::underline(int $red, int $green, int $blue)` - apply an RGB color to an underline (see composite styles [below](#composite-styles))

### Composite Styles

It's possible to apply more than one style to your text by using the `Style\Composite()` class. For example, to apply color to both the foreground and background:

```php
use BeBat\ConsoleColor\Style;

$style = new Style();

echo $style->apply(
    'This text is white on red',
    new Style\Composite(Style\Color::BrightWhite, Style\BackgroundColor::Red),
) . PHP_EOL;
```

`Style\Composite()` is also required if you want to add color to an underline:

```php
use BeBat\ConsoleColor\Style;

$style = new Style();

echo $style->apply(
    'This text has a typo',
    new Style\Composite(Style\Underline::Wavy, Style\ColorRGB::underline(255, 0, 0)),
) . PHP_EOL;
```

`Style\Composite()` can actually take as many styles as you need to apply:

```php
use BeBat\ConsoleColor\Style;

$style = new Style();

echo $style->apply(
    "Please don't do this\n",
    new Style\Composite(
        Style\Text::Bold,
        Style\Text::Italic,
        Style\Text::Blink,
        Style\Color256::background(34),
        Style\Color256::foreground(227),
    ),
);
```
