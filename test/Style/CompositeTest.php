<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Test\Style;

use BeBat\ConsoleColor\Style\Composite;
use BeBat\ConsoleColor\StyleInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

use function BeBat\Verify\verify;

/**
 * @internal
 *
 * @testdox Composite Style Tests
 */
final class CompositeTest extends MockeryTestCase
{
    /**
     * @testdox Combines ANSI Codes
     */
    public function testAnsiCode(): void
    {
        $style1 = Mockery::mock(StyleInterface::class);
        $style2 = Mockery::mock(StyleInterface::class);
        $style3 = Mockery::mock(StyleInterface::class);

        $style1->expects()
            ->ansiCode()
            ->andReturn('42');
        $style2->expects()
            ->ansiCode()
            ->andReturn('89;3');
        $style3->expects()
            ->ansiCode()
            ->andReturn('24:48:29');

        $subject = new Composite($style1, $style2, $style3);

        verify($subject)->ansiCode()->is()->identicalTo('42;89;3;24:48:29');
    }

    /**
     * @testdox 256 Color Check
     */
    public function testIs256(): void
    {
        $style1 = Mockery::mock(StyleInterface::class);
        $style2 = Mockery::mock(StyleInterface::class);
        $style3 = Mockery::mock(StyleInterface::class);

        $style1->allows()
            ->is256()
            ->andReturn(false);
        $style2->allows()
            ->is256()
            ->andReturn(false);
        $style3->allows()
            ->is256()
            ->andReturn(true);

        verify(new Composite($style1, $style2))->is256()
            ->is()->false();
        verify(new Composite($style1, $style2, $style3))->is256()
            ->is()->true();
    }

    /**
     * @testdox RGB Color Check
     */
    public function testIsRgb(): void
    {
        $style1 = Mockery::mock(StyleInterface::class);
        $style2 = Mockery::mock(StyleInterface::class);
        $style3 = Mockery::mock(StyleInterface::class);

        $style1->allows()
            ->isRGB()
            ->andReturn(false);
        $style2->allows()
            ->isRGB()
            ->andReturn(false);
        $style3->allows()
            ->isRGB()
            ->andReturn(true);

        verify(new Composite($style1, $style2))->isRGB()
            ->is()->false();
        verify(new Composite($style1, $style2, $style3))->isRGB()
            ->is()->true();
    }
}
