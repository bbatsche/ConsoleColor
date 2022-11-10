<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Test\Style;

use BeBat\ConsoleColor\Style\Color256;
use DomainException;
use PHPUnit\Framework\TestCase;

use function BeBat\Verify\verify;

/**
 * @internal
 *
 * @testdox 256 Color Tests
 */
final class Color256Test extends TestCase
{
    /**
     * @testdox ANSI Code Output
     */
    public function testAnsiCodes(): void
    {
        verify(Color256::foreground(42))->ansiCode()
            ->is()->identicalTo('38:5:42');

        verify(Color256::background(72))->ansiCode()
            ->is()->identicalTo('48:5:72');

        verify(Color256::underline(92))->ansiCode()
            ->is()->identicalTo('58:5:92');
    }

    public function testExceptions(): void
    {
        verify(Color256::class)->foreground(-1)->will()
            ->throwException()->instanceOf(DomainException::class)
            ->withMessage()->identicalTo('Color value must be from 0 to 255.')
            ->foreground(256)->will()
            ->throwException()->instanceOf(DomainException::class)
            ->withMessage()->identicalTo('Color value must be from 0 to 255.')

            ->background(-1)->will()
            ->throwException()->instanceOf(DomainException::class)
            ->withMessage()->identicalTo('Color value must be from 0 to 255.')
            ->background(256)->will()
            ->throwException()->instanceOf(DomainException::class)
            ->withMessage()->identicalTo('Color value must be from 0 to 255.')

            ->underline(-1)->will()
            ->throwException()->instanceOf(DomainException::class)
            ->withMessage()->identicalTo('Color value must be from 0 to 255.')
            ->underline(256)->will()
            ->throwException()->instanceOf(DomainException::class)
            ->withMessage()->identicalTo('Color value must be from 0 to 255.');
    }
}
