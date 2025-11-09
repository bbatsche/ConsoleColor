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
     * @testdox Can be configured to automatically terminate styles
     *
     * @group require-tty
     */
    public function testAutoTermination(): void
    {
        $style = new Style();

        verify($style)->willAutoTerminate()->is()->true()
            ->and()->isActive()->is()->false();

        verify($style)->apply('Some text!', Color::Red)
            ->will()->endWith("\e[0m");
        verify($style)->isActive()->is()->false();

        $style->autoTerminate(false);

        verify($style)->apply('More text!', Color::Green)
            ->willNot()->endWith("\e[0m");
        verify($style)->willAutoTerminate()->is()->false()
            ->and()->isActive()->is()->true();
    }

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
            ->apply('Plain text', Color::Green)->is()->identicalTo('Plain text')
            ->terminate()->is()->identicalTo('');

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
     * @testdox Styles can be manually terminated or non-terminated
     *
     * @group require-tty
     */
    public function testManualTermination(): void
    {
        $style = new Style();

        verify($style)->apply('This is text', Color::Yellow, false)
            ->willNot()->endWith("\e[0m");
        verify($style)->isActive()->is()->true();
        verify($style)->terminate()->is()->identicalTo("\e[0m");
        verify($style)->isActive()->is()->false();

        $style->autoTerminate(false);

        verify($style)->apply('Even more text!', Color::BrightBlue, true)
            ->will()->endWith("\e[0m");
        verify($style)->isActive()->is()->false();

        $style->apply('This text has no style!', Style\Text::None);
        verify($style)->isActive()->is()->false();
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
            ->apply('Styled text', Color::Blue)->is()->identicalTo("\e[34mStyled text\e[0m")
            ->terminate()->is()->identicalTo("\e[0m");

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
            ->apply('Plain text', Color::Green)->is()->identicalTo('Plain text')
            ->terminate()->is()->identicalTo('');
    }
}
