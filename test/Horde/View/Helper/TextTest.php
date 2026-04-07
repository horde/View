<?php

declare(strict_types=1);

/**
 * Copyright 2007-2026 Maintainable Software, LLC
 * Copyright 2008-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\View\Helper;

use Horde_View;
use Horde_View_Helper_Text;
use Horde_View_Helper_Text_Cycle;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Text::class)]
#[CoversClass(Horde_View_Helper_Text_Cycle::class)]
class TextTest extends TestCase
{
    private Horde_View $view;

    public function setUp(): void
    {
        $this->view = new Horde_View();
        $this->view->addHelper('Text');
    }

    public function testEscape(): void
    {
        $text = "Test 'escaping html' \"quotes\" and & amps";
        $expected = "Test &#039;escaping html&#039; &quot;quotes&quot; and &amp; amps";
        $this->assertEquals($expected, $this->view->h($text));
    }

    public function testTruncate(): void
    {
        $str = 'The quick brown fox jumps over the lazy dog tomorrow morning.';
        $expected = 'The quick brown fox jumps over the la...';
        $this->assertEquals($expected, $this->view->truncate($str, 40));
    }

    public function testTruncateEmpty(): void
    {
        $this->assertEquals('', $this->view->truncate('', 40));
    }

    public function testTruncateShortString(): void
    {
        $str = 'Short';
        $this->assertEquals('Short', $this->view->truncate($str, 40));
    }

    public function testTruncateMiddle(): void
    {
        $str = 'The quick brown fox jumps over the lazy dog tomorrow morning.';
        $expected = 'The quick brown fox...tomorrow morning.';
        $this->assertEquals($expected, $this->view->truncateMiddle($str, 40));
    }

    public function testTruncateMiddleTooShort(): void
    {
        $str = 'The quick brown fox jumps over the dog.';
        $expected = 'The quick brown fox jumps over the dog.';
        $this->assertEquals($expected, $this->view->truncateMiddle($str, 40));
    }

    public function testHighlightDefault(): void
    {
        $str = 'The quick brown fox jumps over the dog.';
        $expected = 'The quick <strong class="highlight">brown</strong> fox jumps over the dog.';
        $this->assertEquals($expected, $this->view->highlight($str, 'brown'));
    }

    public function testHighlightCustom(): void
    {
        $str = 'The quick brown fox jumps over the dog.';
        $expected = 'The quick <em>brown</em> fox jumps over the dog.';
        $this->assertEquals($expected, $this->view->highlight($str, 'brown', '<em>$1</em>'));
    }

    public function testHighlightNoMatch(): void
    {
        $str = 'The quick brown fox jumps over the dog.';
        $this->assertEquals($str, $this->view->highlight($str, 'black'));
    }

    public function testHighlightEmptyPhrase(): void
    {
        $str = 'The quick brown fox';
        $this->assertEquals($str, $this->view->highlight($str, ''));
    }

    public function testMakeBreakable(): void
    {
        $this->assertEquals(
            'path/<wbr>to/<wbr>file_<wbr>name',
            $this->view->makeBreakable('path/to/file_name')
        );
    }

    public function testMakeBreakableEmpty(): void
    {
        $this->assertEquals('', $this->view->makeBreakable(''));
    }

    public function testCleanSmartQuotes(): void
    {
        // Test em-dash (UTF-8)
        $this->assertEquals('--', $this->view->cleanSmartQuotes("\xE2\x80\x94"));
        // Test en-dash (UTF-8)
        $this->assertEquals('-', $this->view->cleanSmartQuotes("\xE2\x80\x93"));
        // Test left single quote (UTF-8)
        $this->assertEquals("'", $this->view->cleanSmartQuotes("\xE2\x80\x98"));
        // Test right single quote (UTF-8)
        $this->assertEquals("'", $this->view->cleanSmartQuotes("\xE2\x80\x99"));
        // Test left double quote (UTF-8)
        $this->assertEquals('"', $this->view->cleanSmartQuotes("\xE2\x80\x9C"));
        // Test right double quote (UTF-8)
        $this->assertEquals('"', $this->view->cleanSmartQuotes("\xE2\x80\x9D"));
        // Test ellipsis (UTF-8)
        $this->assertEquals('...', $this->view->cleanSmartQuotes("\xE2\x80\xA6"));
    }

    public function testCleanSmartQuotesTab(): void
    {
        $this->assertEquals(' ', $this->view->cleanSmartQuotes("\x09"));
    }

    public function testCycleClass(): void
    {
        $value = new Horde_View_Helper_Text_Cycle(['one', 2, '3']);

        $this->assertEquals('one', (string) $value);
        $this->assertEquals('2', (string) $value);
        $this->assertEquals('3', (string) $value);
        $this->assertEquals('one', (string) $value);
        $value->reset();
        $this->assertEquals('one', (string) $value);
        $this->assertEquals('2', (string) $value);
        $this->assertEquals('3', (string) $value);
    }

    public function testCycleClassWithInvalidArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Horde_View_Helper_Text_Cycle('bad');
    }

    public function testCycleClassWithTooFewValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Horde_View_Helper_Text_Cycle(['foo']);
    }

    public function testCycleResetsWithNewValues(): void
    {
        $this->assertEquals('even', (string) $this->view->cycle('even', 'odd'));
        $this->assertEquals('odd', (string) $this->view->cycle('even', 'odd'));
        $this->assertEquals('even', (string) $this->view->cycle('even', 'odd'));
        $this->assertEquals('1', (string) $this->view->cycle(1, 2, 3));
        $this->assertEquals('2', (string) $this->view->cycle(1, 2, 3));
        $this->assertEquals('3', (string) $this->view->cycle(1, 2, 3));
    }

    public function testNamedCycles(): void
    {
        $this->assertEquals('1', (string) $this->view->cycle(1, 2, 3, ['name' => 'numbers']));
        $this->assertEquals('red', (string) $this->view->cycle('red', 'blue', ['name' => 'colors']));
        $this->assertEquals('2', (string) $this->view->cycle(1, 2, 3, ['name' => 'numbers']));
        $this->assertEquals('blue', (string) $this->view->cycle('red', 'blue', ['name' => 'colors']));
        $this->assertEquals('3', (string) $this->view->cycle(1, 2, 3, ['name' => 'numbers']));
        $this->assertEquals('red', (string) $this->view->cycle('red', 'blue', ['name' => 'colors']));
    }

    public function testDefaultNamedCycle(): void
    {
        $this->assertEquals('1', (string) $this->view->cycle(1, 2, 3));
        $this->assertEquals('2', (string) $this->view->cycle(1, 2, 3, ['name' => 'default']));
        $this->assertEquals('3', (string) $this->view->cycle(1, 2, 3));
    }

    public function testResetCycle(): void
    {
        $this->assertEquals('1', (string) $this->view->cycle(1, 2, 3));
        $this->assertEquals('2', (string) $this->view->cycle(1, 2, 3));
        $this->view->resetCycle();
        $this->assertEquals('1', (string) $this->view->cycle(1, 2, 3));
    }

    public function testResetUnknownCycle(): void
    {
        $ret = $this->view->resetCycle('colors');
        $this->assertNull($ret);
    }

    public function testResetNamedCycle(): void
    {
        $this->assertEquals('1', (string) $this->view->cycle(1, 2, 3, ['name' => 'numbers']));
        $this->assertEquals('red', (string) $this->view->cycle('red', 'blue', ['name' => 'colors']));
        $this->view->resetCycle('numbers');
        $this->assertEquals('1', (string) $this->view->cycle(1, 2, 3, ['name' => 'numbers']));
        $this->assertEquals('blue', (string) $this->view->cycle('red', 'blue', ['name' => 'colors']));
        $this->assertEquals('2', (string) $this->view->cycle(1, 2, 3, ['name' => 'numbers']));
        $this->assertEquals('red', (string) $this->view->cycle('red', 'blue', ['name' => 'colors']));
    }

    public function testPluralization(): void
    {
        $this->assertEquals('1 count', $this->view->pluralize(1, 'count'));
        $this->assertEquals('2 counts', $this->view->pluralize(2, 'count'));
        $this->assertEquals('1 count', $this->view->pluralize('1', 'count'));
        $this->assertEquals('2 counts', $this->view->pluralize('2', 'count'));
        $this->assertEquals('1,066 counts', $this->view->pluralize('1,066', 'count'));
        $this->assertEquals('1.25 counts', $this->view->pluralize('1.25', 'count'));
        $this->assertEquals('2 counters', $this->view->pluralize('2', 'count', 'counters'));
    }
}
