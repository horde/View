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
use Horde_View_Helper_Number;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Number::class)]
class NumberTest extends TestCase
{
    private Horde_View_Helper_Number $helper;

    public function setUp(): void
    {
        $this->helper = new Horde_View_Helper_Number(new Horde_View());
    }

    public function testNumberToHumanSize(): void
    {
        setlocale(LC_NUMERIC, 'C');
        $this->assertEquals('0 Bytes', $this->helper->numberToHumanSize(0));
        $this->assertEquals('0 Bytes', $this->helper->numberToHumanSize(0));
        $this->assertEquals('1 Byte', $this->helper->numberToHumanSize(1));
        $this->assertEquals('3 Bytes', $this->helper->numberToHumanSize(3.14159265));
        $this->assertEquals('123 Bytes', $this->helper->numberToHumanSize(123.0));
        $this->assertEquals('123 Bytes', $this->helper->numberToHumanSize(123));
        $this->assertEquals('1.2 KB', $this->helper->numberToHumanSize(1234));
        $this->assertEquals('12.1 KB', $this->helper->numberToHumanSize(12345));
        $this->assertEquals('1.2 MB', $this->helper->numberToHumanSize(1234567));
        $this->assertEquals('1.1 GB', $this->helper->numberToHumanSize(1234567890));
        $this->assertEquals('1.1 TB', $this->helper->numberToHumanSize(1234567890123));
        $this->assertEquals('444 KB', $this->helper->numberToHumanSize(444 * 1024));
        $this->assertEquals('1023 MB', $this->helper->numberToHumanSize(1023 * 1048576));
        $this->assertEquals('3 TB', $this->helper->numberToHumanSize(3 * 1099511627776));
        $this->assertEquals('1.18 MB', $this->helper->numberToHumanSize(1234567, 2));
        $this->assertEquals('3 Bytes', $this->helper->numberToHumanSize(3.14159265, 4));
        $this->assertEquals("123 Bytes", $this->helper->numberToHumanSize("123"));
        $this->assertNull($this->helper->numberToHumanSize('x'));
        $this->assertNull($this->helper->numberToHumanSize(null));
    }
}
