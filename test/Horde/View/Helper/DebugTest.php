<?php
/**
 * Copyright 2007-2008 Maintainable Software, LLC
 * Copyright 2006-2017 Horde LLC (http://www.horde.org/)
 *
 * @author     Mike Naberezny <mike@maintainable.com>
 * @author     Derek DeVries <derek@maintainable.com>
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @license    http://www.horde.org/licenses/bsd
 * @category   Horde
 * @package    View
 * @subpackage UnitTests
 */

/**
 * @group      view
 * @author     Mike Naberezny <mike@maintainable.com>
 * @author     Derek DeVries <derek@maintainable.com>
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @license    http://www.horde.org/licenses/bsd
 * @category   Horde
 * @package    View
 * @subpackage UnitTests
 */
#[AllowDynamicProperties]
class Horde_View_Helper_DebugTest extends Horde_Test_Case
{
    public function setUp(): void
    {
        $this->helper = new Horde_View_Helper_Debug(new Horde_View());
    }

    // test truncate
    public function testDebug()
    {
        $xdebug = ini_get('xdebug.overload_var_dump');
        ini_set('xdebug.overload_var_dump', 0);
        $expected = '<pre class="debug_dump">string(7) &quot;foo&amp;bar&quot;';
        $output = $this->helper->debug('foo&bar');
        ini_set('xdebug.overload_var_dump', $xdebug);
        $this->assertStringContainsString($expected, $output);
    }

}
