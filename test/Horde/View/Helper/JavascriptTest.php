<?php

declare(strict_types=1);

/**
 * Copyright 2007-2026 Maintainable Software, LLC
 * Copyright 2006-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\View\Helper;

use Horde_View;
use Horde_View_Helper_Javascript;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Javascript::class)]
class JavascriptTest extends TestCase
{
    private Horde_View $view;

    public function setUp(): void
    {
        $this->view = new Horde_View();
        $this->view->addHelper('Javascript');
        $this->view->addHelper('Tag');
    }

    public function testJavascriptTag(): void
    {
        $this->assertEquals(
            "<script type=\"text/javascript\">\n//<![CDATA[\nfoo = 1;\n//]]>\n</script>",
            $this->view->javascriptTag('foo = 1;')
        );
    }

    public function testEscapeJavascript(): void
    {
        // Backslash is replaced with \0\0 per the implementation
        $this->assertEquals('\0\0', $this->view->escapeJavascript('\\'));
        $this->assertEquals('\\n', $this->view->escapeJavascript("\n"));
        $this->assertEquals('\\n', $this->view->escapeJavascript("\r\n"));
        $this->assertEquals('\\n', $this->view->escapeJavascript("\r"));
        $this->assertEquals('\\"', $this->view->escapeJavascript('"'));
        $this->assertEquals("\\'", $this->view->escapeJavascript("'"));
    }

    public function testJavascriptCdataSection(): void
    {
        $this->assertEquals(
            "\n//<![CDATA[\nfoo = 1;\n//]]>\n",
            $this->view->javascriptCdataSection('foo = 1;')
        );
    }
}
