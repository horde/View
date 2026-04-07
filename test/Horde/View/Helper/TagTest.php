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
use Horde_View_Helper_Tag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Tag::class)]
class TagTest extends TestCase
{
    private Horde_View $view;

    public function setUp(): void
    {
        $this->view = new Horde_View();
        $this->view->addHelper('Tag');
    }

    public function testTag(): void
    {
        $this->assertEquals('<br />', $this->view->tag('br'));
        $this->assertEquals(
            '<br clear="left" />',
            $this->view->tag('br', ['clear' => 'left'])
        );
    }

    public function testTagOptions(): void
    {
        $this->assertMatchesRegularExpression(
            '/\A<p class="(show|elsewhere)" \\/>\z/',
            $this->view->tag('p', ['class' => 'show',
                'class' => 'elsewhere'])
        );
    }

    public function testTagOptionsRejectsNullOption(): void
    {
        $this->assertEquals(
            '<p />',
            $this->view->tag('p', ['ignored' => null])
        );
    }

    public function testTagOptionsAcceptsBlankOption(): void
    {
        $this->assertEquals(
            '<p included="" />',
            $this->view->tag('p', ['included' => ''])
        );
    }

    public function testTagOptionsConvertsBooleanOption(): void
    {
        $this->assertEquals(
            '<p disabled multiple readonly />',
            $this->view->tag('p', ['disabled' => true,
                'multiple' => true,
                'readonly' => true])
        );
    }

    public function testContentTag(): void
    {
        $this->assertEquals(
            '<a href="create">Create</a>',
            $this->view->contentTag('a', 'Create', ['href' => 'create'])
        );
    }

    public function testCdataSection(): void
    {
        $this->assertEquals('<![CDATA[<hello world>]]>', $this->view->cdataSection('<hello world>'));
    }

    public function testEscapeOnce(): void
    {
        $this->assertEquals('1 &lt; 2 &amp; 3', $this->view->escapeOnce('1 < 2 &amp; 3'));
    }

    public function testDoubleEscapingAttributes(): void
    {
        $attributes = ['1&amp;2', '1 &lt; 2', '&#8220;test&#8220;'];
        foreach ($attributes as $escaped) {
            $this->assertEquals(
                "<a href=\"$escaped\" />",
                $this->view->tag('a', ['href' => $escaped])
            );
        }
    }

    public function testSkipInvalidEscapedAttributes(): void
    {
        $attributes = ['&1;', '&#1dfa3;', '& #123;'];
        foreach ($attributes as $escaped) {
            $this->assertEquals(
                '<a href="' . str_replace('&', '&amp;', $escaped) . '" />',
                $this->view->tag('a', ['href' => $escaped])
            );
        }
    }
}
