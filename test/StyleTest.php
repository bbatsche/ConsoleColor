<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Test;

use BeBat\ConsoleColor\Style;
use BeBat\ConsoleColor\Style\Color;
use BeBat\ConsoleColor\Style\Color256;
use BeBat\ConsoleColor\Style\ColorRGB;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zalas\PHPUnit\Globals\Attribute\Putenv;

use function BeBat\Verify\verify;

/**
 * @internal
 *
 * @testdox Style Utility Tests
 */
#[Putenv('FORCE_COLOR', '1')]
final class StyleTest extends MockeryTestCase
{
    /**
     * @testdox Checks COLORTERM variable
     *
     * @group require-tty
     */
    #[Putenv('COLORTERM', 'truecolor')]
    public function testCheckColorterm(): void
    {
        verify(new Style())
            ->supportsStyles()->is()->true()
            ->supports256Colors()->is()->true()
            ->supportsRGBColors()->is()->true()
            ->apply()
            ->with('Styled text', ColorRGB::foreground(42, 56, 128))->is()->identicalTo("\e[38:2:42:56:128mStyled text\e[0m");
    }

    /**
     * @testdox Checks if the resource is a TTY
     */
    #[Putenv('FORCE_COLOR', unset: true)]
    public function testCheckIfResourceIsTty(): void
    {
        /** @var resource */
        $resource = fopen(__DIR__ . '/tmp/foo', 'w');

        verify(new Style($resource))
            ->supportsStyles->is()->false()
            ->apply('Plain text', Color::Green)->is()->identicalTo('Plain text');

        fclose($resource);
    }

    /**
     * @testdox Checks TERM variable
     *
     * @group require-tty
     */
    #[Putenv('TERM', 'xterm-256color')]
    #[Putenv('COLORTERM', unset: true)]
    public function testCheckTerm(): void
    {
        verify(new Style())
            ->supportsStyles()->is()->true()
            ->supports256Colors()->is()->true()
            ->supportsRGBColors()->is()->false()
            ->apply()
            ->with('Plain text', ColorRGB::foreground(42, 56, 128))->is()->identicalTo('Plain text')
            ->with('Styled text', Color256::foreground(200))->is()->identicalTo("\e[38:5:200mStyled text\e[0m");
    }

    /**
     * @testdox Can generate esc sequence codes
     */
    public function testEscSequence(): void
    {
        verify(new Style())
            ->escSequence('foo')->is()->identicalTo("\e[foom");
    }

    /**
     * @testdox Falls back on ANSI styles
     *
     * @group require-tty
     */
    #[Putenv('TERM', unset: true)]
    #[Putenv('COLORTERM', unset: true)]
    public function testFallbackOnAnsiStyle(): void
    {
        verify(new Style())
            ->supportsStyles()->is()->true()
            ->supports256Colors()->is()->false()
            ->supportsRGBColors()->is()->false()
            ->apply()
            ->with('Plain text', ColorRGB::foreground(42, 56, 128))->is()->identicalTo('Plain text')
            ->with('Plain text', Color256::foreground(200))->is()->identicalTo('Plain text')
            ->with('Styled text', Color::Cyan)->is()->identicalTo("\e[36mStyled text\e[0m");
    }

    /**
     * @testdox Styles can be forced on
     */
    #[Putenv('FORCE_COLOR', unset: true)]
    public function testOverrideSupportChecks(): void
    {
        /** @var resource */
        $resource = fopen(__DIR__ . '/tmp/foo', 'w');
        $subject  = new Style($resource);
        $subject->force();

        verify($subject)
            ->supportsStyles->is()->false()
            ->apply('Styled text', Color::Blue)->is()->identicalTo("\e[34mStyled text\e[0m");

        fclose($resource);
    }

    /**
     * @testdox Respects NO_COLOR variable
     */
    #[Putenv('NO_COLOR', '')]
    public function testRespectsNoColor(): void
    {
        verify(new Style())
            ->supportsStyles()->is()->false()
            ->supports256Colors()->is()->false()
            ->supportsRGBColors()->is()->false()
            ->apply('Plain text', Color::Green)->is()->identicalTo('Plain text');
    }
}
