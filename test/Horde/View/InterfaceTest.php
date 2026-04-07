<?php

declare(strict_types=1);

/**
 * Copyright 2006-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\View;

use Horde_View;
use Horde_View_Interface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View::class)]
class InterfaceTest extends TestCase
{
    public function testViewInterface(): void
    {
        eval('class Test_View_Interface extends Horde_View implements Horde_View_Interface {};');
        $view = new \Test_View_Interface();
        $this->assertInstanceOf(Horde_View::class, $view);
        $this->assertInstanceOf(Horde_View_Interface::class, $view);
    }
}
