<?php

declare(strict_types=1);

namespace BeBat\ConsoleColor\Test;

use BeBat\ConsoleColor\Style;
use BeBat\ConsoleColor\Style\Color;
use BeBat\ConsoleColor\Style\Color256;
use BeBat\ConsoleColor\Style\ColorRGB;
use Mockery\Adapter\Phpunit\MockeryTestCase;

use function BeBat\Verify\verify;

/**
 * @internal
 *
 * @testdox Style Utility Tests
 */
final class StyleTest extends MockeryTestCase
{
    /**
     * @putenv COLORTERM=truecolor
     *
     * @testdox Checks COLORTERM variable
     *
     * @group require-tty
     */
    public function testCheckColorterm(): void
    {
        verify(new Style())
            ->supportsStyles->is()->true()
            ->supports256Colors->is()->true()
            ->supportsRGBColors->is()->true()
            ->apply()
            ->with('Styled text', ColorRGB::foreground(42, 56, 128))->is()->identicalTo("\033[38:2:42:56:128mStyled text\033[0m");
    }

    /**
     * @testdox Checks if the resource is a TTY
     */
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
     * @putenv TERM=xterm-256color
     *
     * @unset-getenv COLORTERM
     *
     * @testdox Checks TERM variable
     *
     * @group require-tty
     */
    public function testCheckTerm(): void
    {
        verify(new Style())
            ->supportsStyles->is()->true()
            ->supports256Colors->is()->true()
            ->supportsRGBColors->is()->false()
            ->apply()
            ->with('Plain text', ColorRGB::foreground(42, 56, 128))->is()->identicalTo('Plain text')
            ->with('Styled text', Color256::foreground(200))->is()->identicalTo("\033[38:5:200mStyled text\033[0m");
    }

    /**
     * @testdox Can generate esc sequence codes
     */
    public function testEscSequence(): void
    {
        verify(new Style())
            ->escSequence('foo')->is()->identicalTo("\033[foom");
    }

    /**
     * @unset-getenv TERM
     * @unset-getenv COLORTERM
     *
     * @testdox Falls back on ANSI styles
     *
     * @group require-tty
     */
    public function testFallbackOnAnsiStyle(): void
    {
        verify(new Style())
            ->supportsStyles->is()->true()
            ->supports256Colors->is()->false()
            ->supportsRGBColors->is()->false()
            ->apply()
            ->with('Plain text', ColorRGB::foreground(42, 56, 128))->is()->identicalTo('Plain text')
            ->with('Plain text', Color256::foreground(200))->is()->identicalTo('Plain text')
            ->with('Styled text', Color::Cyan)->is()->identicalTo("\033[36mStyled text\033[0m");
    }

    /**
     * @testdox Styles can be forced on
     */
    public function testOverrideSupportChecks(): void
    {
        /** @var resource */
        $resource = fopen(__DIR__ . '/tmp/foo', 'w');
        $subject  = new Style($resource);
        $subject->force();

        verify($subject)
            ->supportsStyles->is()->false()
            ->apply('Styled text', Color::Blue)->is()->identicalTo("\033[34mStyled text\033[0m");

        fclose($resource);
    }

    /**
     * @putenv NO_COLOR
     *
     * @testdox Respects NO_COLOR variable
     */
    public function testRespectsNoColor(): void
    {
        verify(new Style())
            ->supportsStyles->is()->false()
            ->supports256Colors->is()->false()
            ->supportsRGBColors->is()->false()
            ->apply('Plain text', Color::Green)->is()->identicalTo('Plain text');
    }
}
