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
use Horde_View_Helper_Debug;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Debug::class)]
class DebugTest extends TestCase
{
    private Horde_View_Helper_Debug $helper;

    public function setUp(): void
    {
        $this->helper = new Horde_View_Helper_Debug(new Horde_View());
    }

    public function testDebug(): void
    {
        $xdebug = ini_get('xdebug.overload_var_dump');
        ini_set('xdebug.overload_var_dump', '0');
        $expected = '<pre class="debug_dump">string(7) &quot;foo&amp;bar&quot;';
        $output = $this->helper->debug('foo&bar');
        ini_set('xdebug.overload_var_dump', $xdebug ?: '0');
        $this->assertStringContainsString($expected, $output);
    }

    public function testDebugArray(): void
    {
        $xdebug = ini_get('xdebug.overload_var_dump');
        ini_set('xdebug.overload_var_dump', '0');
        $output = $this->helper->debug(['key' => 'value']);
        ini_set('xdebug.overload_var_dump', $xdebug ?: '0');
        $this->assertStringContainsString('<pre class="debug_dump">', $output);
        $this->assertStringContainsString('array', $output);
    }

    public function testDebugNull(): void
    {
        $xdebug = ini_get('xdebug.overload_var_dump');
        ini_set('xdebug.overload_var_dump', '0');
        $output = $this->helper->debug(null);
        ini_set('xdebug.overload_var_dump', $xdebug ?: '0');
        $this->assertStringContainsString('NULL', $output);
    }

    public function testDebugInteger(): void
    {
        $xdebug = ini_get('xdebug.overload_var_dump');
        ini_set('xdebug.overload_var_dump', '0');
        $output = $this->helper->debug(42);
        ini_set('xdebug.overload_var_dump', $xdebug ?: '0');
        $this->assertStringContainsString('int(42)', $output);
    }
}
