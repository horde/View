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
namespace Horde\View\Helper;
use \Horde_View;
use \Horde_View_Helper_Capture;
use \PHPUnit\Framework\TestCase;
use \Horde_View_Exception as ViewException;
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
class CaptureTest extends TestCase
{
    public function setUp(): void
    {
        $this->view   = new Horde_View();
        $this->helper = new Horde_View_Helper_Capture($this->view);
    }

    public function testCapture()
    {
        $capture = $this->helper->capture();
        echo $expected = '<span>foo</span>';

        $this->assertEquals($expected, $capture->end());
    }

    public function testCaptureThrowsWhenAlreadyEnded()
    {
        $this->expectException(ViewException::class);
        $this->expectExceptionMessage('Capture already ended');
        $capture = $this->helper->capture();
        $capture->end();
        $capture->end();
    }

    public function testContentFor()
    {
        $capture = $this->helper->contentFor('foo');
        echo $expected = '<span>foo</span>';
        $capture->end();

        $this->assertEquals($expected, $this->view->contentForFoo);
    }

}
