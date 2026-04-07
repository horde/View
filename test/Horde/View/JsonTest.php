<?php

declare(strict_types=1);

/**
 * Copyright 2006-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\View;

use Horde_View_Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Json::class)]
class JsonTest extends TestCase
{
    public function testRenderEmpty(): void
    {
        $view = new Horde_View_Json();
        $json = $view->render();
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
    }

    public function testRenderWithProperties(): void
    {
        $view = new Horde_View_Json();
        $view->title = 'Hello World';
        $view->count = 42;

        $json = $view->render();
        $decoded = json_decode($json, true);

        $this->assertEquals('Hello World', $decoded['title']);
        $this->assertEquals(42, $decoded['count']);
    }

    public function testRenderIgnoresTemplateName(): void
    {
        $view = new Horde_View_Json();
        $view->name = 'test';

        $json1 = $view->render('template1');
        $json2 = $view->render('template2');

        $this->assertEquals($json1, $json2);
    }
}
