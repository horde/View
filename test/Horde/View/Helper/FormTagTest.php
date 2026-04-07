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

use DOMDocument;
use Horde_View;
use Horde_View_Helper_FormTag;
use Horde\View\Mock\FormTagUrlHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_FormTag::class)]
class FormTagTest extends TestCase
{
    private Horde_View $view;

    public function setUp(): void
    {
        $this->view = new Horde_View();
        $this->view->addHelper('FormTag');
        $this->view->addHelper('Tag');
        $this->view->addHelper(new FormTagUrlHelper($this->view));
    }

    public function testFormTag(): void
    {
        $actual   = $this->view->formTag();
        $expected = '<form action="http://www.example.com" method="post">';
        $this->assertEquals($expected, $actual);
    }

    public function testFormTagWithExplicitUrl(): void
    {
        $actual   = $this->view->formTag('/controller/action');
        $expected = '<form action="/controller/action" method="post">';
        $this->assertEquals($expected, $actual);
    }

    public function testFormTagMultipart(): void
    {
        $actual   = $this->view->formTag([], ['multipart' => true]);
        $expected = '<form action="http://www.example.com" enctype="multipart/form-data" method="post">';
        $this->assertEquals($expected, $actual);
    }

    public function testFormTagWithMethod(): void
    {
        $actual   = $this->view->formTag([], ['method' => 'put']);
        $expected = '<form action="http://www.example.com" method="post"><div style="margin:0;padding:0"><input name="_method" type="hidden" value="put" /></div>';
        $this->assertEquals($expected, $actual);
    }

    public function testCheckBoxTag(): void
    {
        $actual   = $this->view->checkBoxTag('admin');
        $expected = '<input id="admin" name="admin" type="checkbox" value="1" />';
        $this->assertHtmlDomEquals($expected, $actual);
    }

    public function testHiddenFieldTag(): void
    {
        $actual   = $this->view->hiddenFieldTag('id', 3);
        $expected = '<input id="id" name="id" type="hidden" value="3" />';
        $this->assertHtmlDomEquals($expected, $actual);
    }

    public function testFileFieldTag(): void
    {
        $actual   = $this->view->fileFieldTag('id');
        $expected = '<input id="id" name="id" type="file" />';
        $this->assertHtmlDomEquals($expected, $actual);
    }

    public function testPasswordFieldTag(): void
    {
        $actual   = $this->view->passwordFieldTag();
        $expected = '<input id="password" name="password" type="password" />';
        $this->assertHtmlDomEquals($expected, $actual);
    }

    public function testRadioButtonTag(): void
    {
        $actual   = $this->view->radioButtonTag('people', 'david');
        $expected = '<input id="people_david" name="people" type="radio" value="david" />';
        $this->assertHtmlDomEquals($expected, $actual);

        $actual   = $this->view->radioButtonTag('num_people', 5);
        $expected = '<input id="num_people_5" name="num_people" type="radio" value="5" />';
        $this->assertHtmlDomEquals($expected, $actual);

        $actual   = $this->view->radioButtonTag('gender', 'm')
                  . $this->view->radioButtonTag('gender', 'f');
        $expected = '<input id="gender_m" name="gender" type="radio" value="m" />'
                  . '<input id="gender_f" name="gender" type="radio" value="f" />';
        $this->assertEquals($expected, $actual);

        $actual   = $this->view->radioButtonTag('opinion', '-1')
                  . $this->view->radioButtonTag('opinion', '1');
        $expected = '<input id="opinion_-1" name="opinion" type="radio" value="-1" />'
                  . '<input id="opinion_1" name="opinion" type="radio" value="1" />';
        $this->assertEquals($expected, $actual);
    }

    public function testSelectTag(): void
    {
        $actual   = $this->view->selectTag('people', '<option>david</option>');
        $expected = '<select id="people" name="people"><option>david</option></select>';
        $this->assertHtmlDomEquals($expected, $actual);
    }

    public function testTextAreaTagSizeString(): void
    {
        $actual   = $this->view->textAreaTag('body', 'hello world', ['size' => '20x40']);
        $expected = '<textarea cols="20" id="body" name="body" rows="40">hello world</textarea>';
        $this->assertHtmlDomEquals($expected, $actual);
    }

    public function testTextAreaTagShouldDisregardSizeIfGivenAsAnInteger(): void
    {
        $actual   = $this->view->textAreaTag('body', 'hello world', ['size' => 20]);
        $expected = '<textarea id="body" name="body">hello world</textarea>';
        $this->assertHtmlDomEquals($expected, $actual);
    }

    public function testTextFieldTag(): void
    {
        $actual   = $this->view->textFieldTag('title', 'Hello!');
        $expected = '<input id="title" name="title" type="text" value="Hello!" />';
        $this->assertHtmlDomEquals($expected, $actual);
    }

    public function testTextFieldTagClassString(): void
    {
        $actual   = $this->view->textFieldTag('title', 'Hello!', ['class' => 'admin']);
        $expected = '<input class="admin" id="title" name="title" type="text" value="Hello!" />';
        $this->assertHtmlDomEquals($expected, $actual);
    }

    public function testBooleanOptions(): void
    {
        $this->assertHtmlDomEquals(
            '<input checked="checked" disabled="disabled" id="admin" name="admin" readonly="readonly" type="checkbox" value="1" />',
            $this->view->checkBoxTag("admin", 1, true, ['disabled' => true, 'readonly' => "yes"])
        );

        $this->assertHtmlDomEquals(
            '<input checked="checked" id="admin" name="admin" type="checkbox" value="1" />',
            $this->view->checkBoxTag('admin', 1, true, ['disabled' => false, 'readonly' => null])
        );

        $this->assertHtmlDomEquals(
            '<select id="people" multiple="multiple" name="people"><option>david</option></select>',
            $this->view->selectTag('people', '<option>david</option>', ['multiple' => true])
        );

        $this->assertHtmlDomEquals(
            '<select id="people" name="people"><option>david</option></select>',
            $this->view->selectTag('people', '<option>david</option>', ['multiple' => null])
        );
    }

    public function testSubmitTag(): void
    {
        $expected = '<input name="commit" onclick="this.setAttribute(\'originalValue\', this.value);this.disabled=true;this.value=\'Saving...\';alert(\'hello!\');result = (this.form.onsubmit ? (this.form.onsubmit() ? this.form.submit() : false) : this.form.submit());if (result == false) { this.value = this.getAttribute(\'originalValue\'); this.disabled = false };return result" type="submit" value="Save" />';
        $actual   = $this->view->submitTag('Save', ['disableWith' => 'Saving...', 'onclick' => "alert('hello!')"]);
        $this->assertHtmlDomEquals($expected, $actual);
    }

    /**
     * Test two HTML strings for equivalency (identical up to reordering of attributes).
     */
    private function assertHtmlDomEquals(string $expected, string $actual, string $message = ''): void
    {
        $expectedDom = new DOMDocument();
        $expectedDom->loadHTML($expected);

        $actualDom = new DOMDocument();
        $actualDom->loadHTML($actual);

        $this->assertEquals($expectedDom->saveHTML(), $actualDom->saveHTML(), $message);
    }
}
