<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Test\Style;

use BeBat\ConsoleColor\Style\ColorRGB;
use DomainException;
use PHPUnit\Framework\TestCase;

use function BeBat\Verify\verify;

/**
 * @internal
 *
 * @testdox RGB Color Tests
 */
final class ColorRGBTest extends TestCase
{
    /**
     * @return array<int[]>
     */
    public function provideExceptionsCases(): array
    {
        return [
            'red underflow'   => ['red' => -1, 'green' => 128, 'blue' => 128],
            'red overflow'    => ['red' => 256, 'green' => 128, 'blue' => 128],
            'blue underflow'  => ['red' => 128, 'green' => 128, 'blue' => -1],
            'blue overflow'   => ['red' => 128, 'green' => 128, 'blue' => 256],
            'green underflow' => ['red' => 128, 'green' => -1, 'blue' => 128],
            'green overflow'  => ['red' => 128, 'green' => 256, 'blue' => 128],
        ];
    }

    /**
     * @testdox ANSI Code Output
     */
    public function testAnsiCodes(): void
    {
        verify(ColorRGB::foreground(5, 10, 15))->ansiCode()
            ->is()->identicalTo('38:2:5:10:15');

        verify(ColorRGB::background(32, 64, 128))->ansiCode()
            ->is()->identicalTo('48:2:32:64:128');

        verify(ColorRGB::underline(9, 27, 81))->ansiCode()
            ->is()->identicalTo('58:2:9:27:81');
    }

    /**
     * @dataProvider provideExceptionsCases
     */
    public function testExceptions(int $red, int $green, int $blue): void
    {
        verify(ColorRGB::class)->foreground($red, $green, $blue)
            ->will()->throwException()->instanceOf(DomainException::class);

        verify(ColorRGB::class)->background($red, $green, $blue)
            ->will()->throwException()->instanceOf(DomainException::class);

        verify(ColorRGB::class)->underline($red, $green, $blue)
            ->will()->throwException()->instanceOf(DomainException::class);
    }
}
