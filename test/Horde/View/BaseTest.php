<?php

declare(strict_types=1);

/**
 * Copyright 2007-2026 Maintainable Software, LLC
 * Copyright 2008-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\View;

use Horde_View;
use Horde_View_Base;
use Horde_View_Helper_Base;
use Horde_View_Helper_Text;
use Horde_View_Exception as ViewException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View::class)]
#[CoversClass(Horde_View_Base::class)]
class BaseTest extends TestCase
{
    private Horde_View $view;

    public function setUp(): void
    {
        $this->view = new Horde_View();
        $this->view->addTemplatePath(__DIR__ . '/fixtures/');
    }

    // --- Assignment ---

    public function testSet(): void
    {
        $this->view->publicVar = 'test';
        $this->assertEquals('test', $this->view->publicVar);
    }

    public function testAssign(): void
    {
        $this->view->assign(['publicVar' => 'test']);
        $this->assertEquals('test', $this->view->publicVar);
    }

    public function testAssignDoesntOverridePrivateVariables(): void
    {
        $this->expectException(ViewException::class);
        $this->view->assign(['_templatePath' => 'test']);
    }

    public function testAssignAllowsUnderscoreVariables(): void
    {
        $this->view->assign(['_private' => 'test']);
        $this->assertEquals('test', $this->view->_private);
    }

    public function testAccessVar(): void
    {
        $this->view->testVar = 'test';
        $this->assertTrue(!empty($this->view->testVar));

        $this->view->testVar2 = '';
        $this->assertTrue(empty($this->view->testVar2));

        $this->assertTrue(isset($this->view->testVar2));
        $this->assertTrue(!isset($this->view->testVar3));
    }

    // --- Template Paths ---

    public function testAddTemplatePath(): void
    {
        $this->view->addTemplatePath('app/views/shared/');

        $expected = ['app/views/shared/',
            __DIR__ . '/fixtures/',
            './'];
        $this->assertEquals($expected, $this->view->getTemplatePaths());
    }

    public function testAddTemplatePathAddSlash(): void
    {
        $this->view->addTemplatePath('app/views/shared');
        $expected = ['app/views/shared/',
            __DIR__ . '/fixtures/',
            './'];
        $this->assertEquals($expected, $this->view->getTemplatePaths());
    }

    public function testSetTemplatePath(): void
    {
        $this->view->setTemplatePath('/some/path');
        $this->assertEquals(['/some/path/'], $this->view->getTemplatePaths());
    }

    // --- Constructor ---

    public function testConstructorWithEncoding(): void
    {
        $view = new Horde_View(['encoding' => 'ISO-8859-1']);
        $this->assertEquals('ISO-8859-1', $view->getEncoding());
    }

    public function testConstructorWithTemplatePath(): void
    {
        $view = new Horde_View(['templatePath' => '/custom/path']);
        $paths = $view->getTemplatePaths();
        $this->assertEquals('/custom/path/', $paths[0]);
    }

    // --- Encoding ---

    public function testGetSetEncoding(): void
    {
        $this->assertEquals('UTF-8', $this->view->getEncoding());
        $this->view->setEncoding('ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $this->view->getEncoding());
    }

    // --- Rendering ---

    public function testRender(): void
    {
        $this->view->myVar = 'test';

        $expected = "<div>test</div>";
        $this->assertEquals($expected, $this->view->render('testRender.html.php'));
    }

    public function testRenderNoExtension(): void
    {
        $this->view->myVar = 'test';

        $expected = "<div>test</div>";
        $this->assertEquals($expected, $this->view->render('testRender'));
    }

    public function testRenderPathOrder(): void
    {
        $this->view->myVar = 'test';

        $expected = "<div>test</div>";
        $this->assertEquals($expected, $this->view->render('testRender'));

        $this->view->addTemplatePath(__DIR__ . '/fixtures/subdir/');
        $expected = "<div>subdir test</div>";
        $this->assertEquals($expected, $this->view->render('testRender'));
    }

    public function testRenderMissingTemplateThrows(): void
    {
        $this->expectException(ViewException::class);
        $this->view->render('nonExistentTemplate');
    }

    // --- Partials ---

    public function testRenderPartial(): void
    {
        $this->view->myVar1 = 'main';
        $this->view->myVar2 = 'partial';

        $expected = '<div>main<p>partial</p></div>';
        $this->assertEquals($expected, $this->view->render('testPartial'));
    }

    public function testRenderPartialObject(): void
    {
        $this->view->myObject = (object) ['string_value' => 'hello world'];
        $expected = '<div><p>hello world</p></div>';
        $this->assertEquals($expected, $this->view->render('testPartialObject'));
    }

    public function testRenderPartialLocals(): void
    {
        $expected = '<div><p>hello world</p></div>';
        $this->assertEquals($expected, $this->view->render('testPartialLocals'));
    }

    public function testRenderPartialCollection(): void
    {
        $this->view->myObjects = [(object) ['string_value' => 'hello'],
            (object) ['string_value' => 'world']];
        $expected = '<div><p>hello</p><p>world</p></div>';
        $this->assertEquals($expected, $this->view->render('testPartialCollection'));
    }

    public function testRenderPartialCollectionEmpty(): void
    {
        $this->view->myObjects = null;

        $expected = '<div></div>';
        $this->assertEquals($expected, $this->view->render('testPartialCollection'));
    }

    public function testRenderPartialCollectionEmptyArray(): void
    {
        $this->view->myObjects = [];

        $expected = '<div></div>';
        $this->assertEquals($expected, $this->view->render('testPartialCollection'));
    }

    public function testRenderPartialModelCollection(): void
    {
        $this->view->myObjects = [(object) ['string_value' => 'name a'], (object) ['string_value' => 'name b']];

        $expected = '<div><p>name a</p><p>name b</p></div>';
        $this->assertEquals($expected, $this->view->render('testPartialCollection'));
    }

    // --- Escape output ---

    public function testEscapeTemplate(): void
    {
        $this->view->myVar = '"escaping"';
        $this->view->addHelper(new Horde_View_Helper_Text($this->view));

        $expected = "<div>test &quot;escaping&quot; quotes</div>";
        $this->assertEquals($expected, $this->view->render('testEscape'));
    }

    // --- Helpers ---

    public function testAddHelperAndCallMethod(): void
    {
        $str = 'The quick brown fox jumps over the lazy dog tomorrow morning.';

        $this->expectException(ViewException::class);
        $this->view->truncateMiddle($str, 40);
    }

    public function testAddHelperMethodOverwrite(): void
    {
        $this->view->addHelper(new Horde_View_Helper_Text($this->view));

        $ret = $this->view->addHelper(new Horde_View_Helper_Text($this->view));
        $this->assertInstanceOf(Horde_View_Helper_Base::class, $ret);
    }

    public function testAddHelperByClassName(): void
    {
        $this->view->addHelper('Text');
        $str = 'The quick brown fox jumps over the lazy dog tomorrow morning.';
        $expected = 'The quick brown fox...tomorrow morning.';
        $this->assertEquals($expected, $this->view->truncateMiddle($str, 40));
    }

    public function testAddHelperNonExistentClassThrows(): void
    {
        $this->expectException(ViewException::class);
        $this->view->addHelper('NonExistentHelper12345');
    }

    public function testCallNonExistentHelperThrows(): void
    {
        $this->expectException(ViewException::class);
        $this->view->someNonExistentMethod();
    }

    public function testThrowOnHelperCollision(): void
    {
        $this->view->addHelper(new Horde_View_Helper_Text($this->view));
        $this->view->throwOnHelperCollision();

        $this->expectException(ViewException::class);
        $this->view->addHelper(new Horde_View_Helper_Text($this->view));
    }

    public function testGetUndefinedPropertyReturnsNull(): void
    {
        $this->assertNull($this->view->undefinedProperty);
    }
}
