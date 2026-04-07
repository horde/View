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
use Horde_View_Helper_Capture;
use Horde_View_Exception as ViewException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Capture::class)]
class CaptureTest extends TestCase
{
    private Horde_View $view;
    private Horde_View_Helper_Capture $helper;

    public function setUp(): void
    {
        $this->view   = new Horde_View();
        $this->helper = new Horde_View_Helper_Capture($this->view);
    }

    public function testCapture(): void
    {
        $capture = $this->helper->capture();
        echo $expected = '<span>foo</span>';

        $this->assertEquals($expected, $capture->end());
    }

    public function testCaptureThrowsWhenAlreadyEnded(): void
    {
        $this->expectException(ViewException::class);
        $this->expectExceptionMessage('Capture already ended');
        $capture = $this->helper->capture();
        $capture->end();
        $capture->end();
    }

    public function testContentFor(): void
    {
        $capture = $this->helper->contentFor('foo');
        echo $expected = '<span>foo</span>';
        $capture->end();

        $this->assertEquals($expected, $this->view->contentForFoo);
    }

    public function testMultipleCaptures(): void
    {
        $capture1 = $this->helper->capture();
        echo 'first';
        $result1 = $capture1->end();

        $capture2 = $this->helper->capture();
        echo 'second';
        $result2 = $capture2->end();

        $this->assertEquals('first', $result1);
        $this->assertEquals('second', $result2);
    }
}
