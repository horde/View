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
use Horde_View_Base;
use Horde_View_Helper_Form;
use Horde_View_Helper_Form_Builder;
use Horde\View\Mock\Builder;
use Horde\View\Mock\UrlHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Form::class)]
#[CoversClass(Horde_View_Helper_Form_Builder::class)]
class FormTest extends TestCase
{
    private Horde_View $view;
    private object $post;

    public function setUp(): void
    {
        $this->view = new Horde_View();
        $this->view->addHelper('Form');
        $this->view->addHelper('FormTag');
        $this->view->addHelper('Tag');
        $this->view->addHelper(new UrlHelper($this->view));

        $this->post = (object) ['title', 'authorName', 'body',
            'secret', 'writtenOn', 'cost'];
        $this->post->title      = 'Hello World';
        $this->post->authorName = '';
        $this->post->body       = 'Back to the hill and over it again!';
        $this->post->secret     = 1;
        $this->post->writtenOn  = mktime(2004, 6, 15);
        $this->post->id         = 123;
        $this->post->id_before_type_cast = 123;

        $this->view->post = $this->post;
    }

    public function testTextField(): void
    {
        $this->assertEquals(
            '<input id="post_title" name="post[title]" size="30" type="text" value="Hello World" />',
            $this->view->textField('post', 'title')
        );

        $this->assertEquals(
            '<input id="post_title" name="post[title]" size="30" type="password" value="Hello World" />',
            $this->view->passwordField('post', 'title')
        );

        $this->assertEquals(
            '<input id="person_name" name="person[name]" size="30" type="password" />',
            $this->view->passwordField("person", "name")
        );
    }

    public function testTextFieldWithEscapes(): void
    {
        $this->post->title = '<b>Hello World</b>';
        $this->assertEquals(
            '<input id="post_title" name="post[title]" size="30" type="text" value="&lt;b&gt;Hello World&lt;/b&gt;" />',
            $this->view->textField('post', 'title')
        );
    }

    public function testTextFieldWithOptions(): void
    {
        $expected = '<input id="post_title" name="post[title]" size="35" type="text" value="Hello World" />';
        $this->assertEquals($expected, $this->view->textField('post', 'title', ['size' => 35]));
    }

    public function testTextFieldAssumingSize(): void
    {
        $expected = '<input id="post_title" maxlength="35" name="post[title]" size="35" type="text" value="Hello World" />';
        $this->assertEquals($expected, $this->view->textField('post', 'title', ['maxlength' => 35]));
    }

    public function testTextFieldDoesntChangeParamValues(): void
    {
        $objectName = 'post[]';
        $expected = '<input id="post_123_title" name="post[123][title]" size="30" type="text" value="Hello World" />';
        $this->assertEquals($expected, $this->view->textField($objectName, 'title'));
        $this->assertEquals($objectName, 'post[]');
    }

    public function testCheckBox(): void
    {
        $this->assertEquals(
            '<input name="post[secret]" type="hidden" value="0" /><input checked id="post_secret" name="post[secret]" type="checkbox" value="1" />',
            $this->view->checkBox('post', 'secret')
        );

        $this->post->secret = 0;

        $this->assertEquals(
            '<input name="post[secret]" type="hidden" value="0" /><input id="post_secret" name="post[secret]" type="checkbox" value="1" />',
            $this->view->checkBox('post', 'secret')
        );

        $this->assertEquals(
            '<input name="post[secret]" type="hidden" value="0" /><input checked id="post_secret" name="post[secret]" type="checkbox" value="1" />',
            $this->view->checkBox('post', 'secret', ['checked' => true])
        );

        $this->post->secret = true;

        $this->assertEquals(
            '<input name="post[secret]" type="hidden" value="0" /><input checked id="post_secret" name="post[secret]" type="checkbox" value="1" />',
            $this->view->checkBox('post', 'secret')
        );
    }

    public function testCheckBoxWithExplicitCheckedAndUncheckedValues(): void
    {
        $this->post->secret = 'on';

        $this->assertEquals(
            '<input name="post[secret]" type="hidden" value="off" /><input checked id="post_secret" name="post[secret]" type="checkbox" value="on" />',
            $this->view->checkBox('post', 'secret', [], 'on', 'off')
        );
    }

    public function testRadioButton(): void
    {
        $this->assertEquals(
            '<input checked id="post_title_hello_world" name="post[title]" type="radio" value="Hello World" />',
            $this->view->radioButton('post', 'title', 'Hello World')
        );

        $this->assertEquals(
            '<input id="post_title_goodbye_world" name="post[title]" type="radio" value="Goodbye World" />',
            $this->view->radioButton('post', 'title', 'Goodbye World')
        );
    }

    public function testRadioButtonIsCheckedWithIntegers(): void
    {
        $this->assertEquals(
            '<input checked id="post_secret_1" name="post[secret]" type="radio" value="1" />',
            $this->view->radioButton('post', 'secret', '1')
        );
    }

    public function testRadioButtonRespectsPassedInId(): void
    {
        $this->assertEquals(
            '<input checked id="foo" name="post[secret]" type="radio" value="1" />',
            $this->view->radioButton('post', 'secret', '1', ['id' => 'foo'])
        );
    }

    public function testTextArea(): void
    {
        $this->assertEquals(
            '<textarea cols="40" id="post_body" name="post[body]" rows="20">Back to the hill and over it again!</textarea>',
            $this->view->textArea('post', 'body')
        );
    }

    public function testTextAreaWithEscapes(): void
    {
        $this->post->body = "Back to <i>the</i> hill and over it again!";
        $this->assertEquals(
            '<textarea cols="40" id="post_body" name="post[body]" rows="20">Back to &lt;i&gt;the&lt;/i&gt; hill and over it again!</textarea>',
            $this->view->textArea('post', 'body')
        );
    }

    public function testTextAreaWithAlternateValue(): void
    {
        $this->assertEquals(
            '<textarea cols="40" id="post_body" name="post[body]" rows="20">Testing alternate values.</textarea>',
            $this->view->textArea('post', 'body', ['value' => 'Testing alternate values.'])
        );
    }

    public function testTextAreaWithSizeOption(): void
    {
        $this->assertEquals(
            '<textarea cols="183" id="post_body" name="post[body]" rows="820">Back to the hill and over it again!</textarea>',
            $this->view->textArea('post', 'body', ['size' => '183x820'])
        );
    }

    public function testExplicitName(): void
    {
        $this->assertEquals(
            '<input id="post_title" name="dont guess" size="30" type="text" value="Hello World" />',
            $this->view->textField("post", "title", ["name" => "dont guess"])
        );

        $this->assertEquals(
            '<textarea cols="40" id="post_body" name="really!" rows="20">Back to the hill and over it again!</textarea>',
            $this->view->textArea("post", "body", ["name" => "really!"])
        );

        $this->assertEquals(
            '<input name="i mean it" type="hidden" value="0" /><input checked id="post_secret" name="i mean it" type="checkbox" value="1" />',
            $this->view->checkBox("post", "secret", ["name" => "i mean it"])
        );
    }

    public function testExplicitId(): void
    {
        $this->assertEquals(
            '<input id="dont guess" name="post[title]" size="30" type="text" value="Hello World" />',
            $this->view->textField("post", "title", ["id" => "dont guess"])
        );

        $this->assertEquals(
            '<textarea cols="40" id="really!" name="post[body]" rows="20">Back to the hill and over it again!</textarea>',
            $this->view->textArea("post", "body", ["id" => "really!"])
        );

        $this->assertEquals(
            '<input name="post[secret]" type="hidden" value="0" /><input checked id="i mean it" name="post[secret]" type="checkbox" value="1" />',
            $this->view->checkBox("post", "secret", ["id" => "i mean it"])
        );
    }

    public function testAutoIndex(): void
    {
        $pid = $this->post->id;

        $this->assertEquals(
            "<input id=\"post_{$pid}_title\" name=\"post[{$pid}][title]\" size=\"30\" type=\"text\" value=\"Hello World\" />",
            $this->view->textField("post[]", "title")
        );

        $this->assertEquals(
            "<textarea cols=\"40\" id=\"post_{$pid}_body\" name=\"post[{$pid}][body]\" rows=\"20\">Back to the hill and over it again!</textarea>",
            $this->view->textArea("post[]", "body")
        );

        $this->assertEquals(
            "<input name=\"post[{$pid}][secret]\" type=\"hidden\" value=\"0\" /><input checked id=\"post_{$pid}_secret\" name=\"post[{$pid}][secret]\" type=\"checkbox\" value=\"1\" />",
            $this->view->checkBox('post[]', 'secret')
        );

        $this->assertEquals(
            "<input checked id=\"post_{$pid}_title_hello_world\" name=\"post[{$pid}][title]\" type=\"radio\" value=\"Hello World\" />",
            $this->view->radioButton('post[]', 'title', 'Hello World')
        );

        $this->assertEquals(
            "<input id=\"post_{$pid}_title_goodbye_world\" name=\"post[{$pid}][title]\" type=\"radio\" value=\"Goodbye World\" />",
            $this->view->radioButton('post[]', 'title', 'Goodbye World')
        );
    }

    public function testFormFor(): void
    {
        ob_start();
        $form = $this->view->formFor('post', $this->post, ['html' => ['id' => 'create-post']]);
        echo $form->textField('title');
        echo $form->textArea('body');
        echo $form->checkBox('secret');
        echo $form->submit('Create post');
        $form->end();

        $expected
          = '<form action="http://www.example.com" id="create-post" method="post">'
          . '<input id="post_title" name="post[title]" size="30" type="text" value="Hello World" />'
          . '<textarea cols="40" id="post_body" name="post[body]" rows="20">Back to the hill and over it again!</textarea>'
          . '<input name="post[secret]" type="hidden" value="0" />'
          . '<input checked id="post_secret" name="post[secret]" type="checkbox" value="1" />'
          . '<input id="post_submit" name="commit" type="submit" value="Create post" />'
          . "</form>";

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testFormForWithMethod(): void
    {
        ob_start();
        $form = $this->view->formFor('post', $this->post, ['html' => ['id'     => 'create-post',
            'method' => 'put']]);
        echo $form->textField('title');
        echo $form->textArea('body');
        echo $form->checkBox('secret');
        $form->end();

        $expected
          = '<form action="http://www.example.com" id="create-post" method="post">'
          . '<div style="margin:0;padding:0"><input name="_method" type="hidden" value="put" /></div>'
          . '<input id="post_title" name="post[title]" size="30" type="text" value="Hello World" />'
          . '<textarea cols="40" id="post_body" name="post[body]" rows="20">Back to the hill and over it again!</textarea>'
          . '<input name="post[secret]" type="hidden" value="0" />'
          . '<input checked id="post_secret" name="post[secret]" type="checkbox" value="1" />'
          . "</form>";

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testFormForWithoutObject(): void
    {
        ob_start();
        $form = $this->view->formFor('post', ['html' => ['id' => 'create-post']]);
        echo $form->textField('title');
        echo $form->textArea('body');
        echo $form->checkBox('secret');
        $form->end();

        $expected
          = '<form action="http://www.example.com" id="create-post" method="post">'
          . '<input id="post_title" name="post[title]" size="30" type="text" value="Hello World" />'
          . '<textarea cols="40" id="post_body" name="post[body]" rows="20">Back to the hill and over it again!</textarea>'
          . '<input name="post[secret]" type="hidden" value="0" />'
          . '<input checked id="post_secret" name="post[secret]" type="checkbox" value="1" />'
          . "</form>";

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testFormForWithIndex(): void
    {
        ob_start();
        $form = $this->view->formFor('post[]', $this->post);
        echo $form->textField('title');
        echo $form->textArea('body');
        echo $form->checkBox('secret');
        $form->end();

        $expected
          = '<form action="http://www.example.com" method="post">'
          . '<input id="post_123_title" name="post[123][title]" size="30" type="text" value="Hello World" />'
          . '<textarea cols="40" id="post_123_body" name="post[123][body]" rows="20">Back to the hill and over it again!</textarea>'
          . '<input name="post[123][secret]" type="hidden" value="0" />'
          . '<input checked id="post_123_secret" name="post[123][secret]" type="checkbox" value="1" />'
          . '</form>';

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testFieldsFor(): void
    {
        ob_start();
        $fields = $this->view->fieldsFor('post', $this->post);
        echo $fields->textField('title');
        echo $fields->textArea('body');
        echo $fields->checkBox('secret');
        $fields->end();

        $expected
          = '<input id="post_title" name="post[title]" size="30" type="text" value="Hello World" />'
          . '<textarea cols="40" id="post_body" name="post[body]" rows="20">Back to the hill and over it again!</textarea>'
          . '<input name="post[secret]" type="hidden" value="0" />'
          . '<input checked id="post_secret" name="post[secret]" type="checkbox" value="1" />';

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testNestedFieldsFor(): void
    {
        ob_start();
        $form = $this->view->formFor('post', $this->post);
        $fields = $form->fieldsFor('comment', $this->post);
        echo $fields->textField('title');
        $fields->end();
        $form->end();

        $expected
            = '<form action="http://www.example.com" method="post">'
            . '<input id="post_comment_title" name="post[comment][title]" size="30" type="text" value="Hello World" />'
            . '</form>';

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testFieldsForWithoutObject(): void
    {
        ob_start();
        $fields = $this->view->fieldsFor('post');
        echo $fields->textField('title');
        echo $fields->textArea('body');
        echo $fields->checkBox('secret');
        $fields->end();

        $expected
          = '<input id="post_title" name="post[title]" size="30" type="text" value="Hello World" />'
          . '<textarea cols="40" id="post_body" name="post[body]" rows="20">Back to the hill and over it again!</textarea>'
          . '<input name="post[secret]" type="hidden" value="0" />'
          . '<input checked id="post_secret" name="post[secret]" type="checkbox" value="1" />';

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testFieldsForObjectWithBracketedName(): void
    {
        ob_start();
        $fields = $this->view->fieldsFor('author[post]', $this->post);
        echo $fields->textField('title');
        $fields->end();

        $this->assertEquals(
            '<input id="author_post_title" name="author[post][title]" size="30" type="text" value="Hello World" />',
            ob_get_clean()
        );
    }

    public function testFormBuilderDoesNotHaveFormForMethod(): void
    {
        $methods = get_class_methods(Horde_View_Helper_Form_Builder::class);
        $this->assertTrue(empty($methods['formFor']));
    }

    public function testFormForAndFieldsFor(): void
    {
        ob_start();
        $postForm = $this->view->formFor('post', $this->post, ['html' => ['id' => 'create-post']]);
        echo $postForm->textField('title');
        echo $postForm->textArea('body');

        $parentFields = $this->view->fieldsFor('parent_post', $this->post);
        echo $parentFields->checkBox('secret');
        $parentFields->end();
        $postForm->end();

        $expected
          = '<form action="http://www.example.com" id="create-post" method="post">'
          . '<input id="post_title" name="post[title]" size="30" type="text" value="Hello World" />'
          . '<textarea cols="40" id="post_body" name="post[body]" rows="20">Back to the hill and over it again!</textarea>'
          . '<input name="parent_post[secret]" type="hidden" value="0" />'
          . '<input checked id="parent_post_secret" name="parent_post[secret]" type="checkbox" value="1" />'
          . '</form>';

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testFormForWithCustomBuilder(): void
    {
        ob_start();
        $form = $this->view->formFor('post', $this->post, ['builder' => Builder::class]);
        echo $form->textField('bar');
        echo $form->foo();
        $form->end();

        $expected
            = '<form action="http://www.example.com" method="post">'
            . '<input id="post_bar" name="post[bar]" size="30" type="text" />'
            . '<foo /></form>';

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testDefaultFormBuilder(): void
    {
        $oldDefaultFormBuilder = Horde_View_Base::$defaultFormBuilder;
        Horde_View_Base::$defaultFormBuilder = Builder::class;

        try {
            ob_start();
            $form = $this->view->formFor('post', $this->post);
            echo $form->textField('bar');
            echo $form->foo();
            $form->end();

            $expected
                = '<form action="http://www.example.com" method="post">'
                . '<input id="post_bar" name="post[bar]" size="30" type="text" />'
                . '<foo /></form>';

            $this->assertEquals($expected, ob_get_clean());
        } finally {
            Horde_View_Base::$defaultFormBuilder = $oldDefaultFormBuilder;
        }
    }

    public function testFieldsForWithCustomBuilder(): void
    {
        ob_start();
        $fields = $this->view->fieldsFor('post', $this->post, ['builder' => Builder::class]);
        echo $fields->textField('bar');
        echo $fields->foo();
        $fields->end();

        $this->assertEquals(
            '<input id="post_bar" name="post[bar]" size="30" type="text" /><foo />',
            ob_get_clean()
        );
    }

    public function testFormForWithHtmlOptionsAddsOptionsToFormTag(): void
    {
        ob_start();
        $form = $this->view->formFor('post', $this->post, ['html' => ['id' => 'some_form',
            'class' => 'some_class']]);
        $form->end();

        $this->assertEquals(
            '<form action="http://www.example.com" class="some_class" id="some_form" method="post"></form>',
            ob_get_clean()
        );
    }

    public function testFormForWithHiddenField(): void
    {
        ob_start();
        $form = $this->view->formFor('post', $this->post);
        echo $form->hiddenField('title');
        $form->end();

        $expected
          = '<form action="http://www.example.com" method="post">'
          . '<input id="post_title" name="post[title]" type="hidden" value="Hello World" />'
          . '</form>';

        $this->assertEquals($expected, ob_get_clean());
    }

    public function testFormForWithFileField(): void
    {
        ob_start();
        $form = $this->view->formFor('post', $this->post);
        echo $form->fileField('title');
        $form->end();

        $expected
          = '<form action="http://www.example.com" method="post">'
          . '<input id="post_title" name="post[title]" size="30" type="file" />'
          . '</form>';

        $this->assertEquals($expected, ob_get_clean());
    }
}
