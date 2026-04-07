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
use Horde_View_Helper_Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Url::class)]
class UrlTest extends TestCase
{
    private Horde_View $view;

    public function setUp(): void
    {
        $this->view = new Horde_View();
        $this->view->addHelper('Url');
        $this->view->addHelper('Tag');
    }

    public function testLinkTagWithStraightUrl(): void
    {
        $this->assertEquals(
            '<a href="http://www.example.com">Hello</a>',
            $this->view->linkTo('Hello', 'http://www.example.com')
        );
    }

    public function testLinkTagWithQuery(): void
    {
        $this->assertEquals(
            '<a href="http://www.example.com?q1=v1&amp;q2=v2">Hello</a>',
            $this->view->linkTo('Hello', 'http://www.example.com?q1=v1&amp;q2=v2')
        );
    }

    public function testLinkTagWithQueryAndNoName(): void
    {
        $this->assertEquals(
            "<a href=\"http://www.example.com?q1=v1&amp;q2=v2\">http://www.example.com?q1=v1&amp;q2=v2</a>",
            $this->view->linkTo(null, 'http://www.example.com?q1=v1&amp;q2=v2')
        );
    }

    public function testLinkTagWithImg(): void
    {
        $this->assertEquals(
            "<a href=\"http://www.example.com\"><img src='/favicon.jpg'></a>",
            $this->view->linkTo("<img src='/favicon.jpg'>", "http://www.example.com")
        );
    }

    public function testLinkToUnless(): void
    {
        // When condition is true, returns name only (no link)
        $this->assertEquals(
            'Showing',
            $this->view->linkToUnless(true, 'Showing', 'http://www.example.com')
        );

        // When condition is false, returns link
        $this->assertEquals(
            '<a href="http://www.example.com">Listing</a>',
            $this->view->linkToUnless(false, 'Listing', 'http://www.example.com')
        );
    }

    public function testLinkToIf(): void
    {
        // When condition is false, returns name only (no link)
        $this->assertEquals(
            'Showing',
            $this->view->linkToIf(false, 'Showing', 'http://www.example.com')
        );

        // When condition is true, returns link
        $this->assertEquals(
            '<a href="http://www.example.com">Listing</a>',
            $this->view->linkToIf(true, 'Listing', 'http://www.example.com')
        );
    }

    public function testMailTo(): void
    {
        $this->assertEquals(
            "<a href=\"mailto:david@loudthinking.com\">david@loudthinking.com</a>",
            $this->view->mailTo("david@loudthinking.com")
        );
        $this->assertEquals(
            "<a href=\"mailto:david@loudthinking.com\">David Heinemeier Hansson</a>",
            $this->view->mailTo("david@loudthinking.com", "David Heinemeier Hansson")
        );
        $this->assertEquals(
            "<a class=\"admin\" href=\"mailto:david@loudthinking.com\">David Heinemeier Hansson</a>",
            $this->view->mailTo("david@loudthinking.com", "David Heinemeier Hansson", ["class" => "admin"])
        );
    }

    public function testMailToWithJavascript(): void
    {
        $this->assertEquals(
            "<script type=\"text/javascript\">eval(unescape('%64%6f%63%75%6d%65%6e%74%2e%77%72%69%74%65%28%27%3c%61%20%68%72%65%66%3d%22%6d%61%69%6c%74%6f%3a%6d%65%40%64%6f%6d%61%69%6e%2e%63%6f%6d%22%3e%4d%79%20%65%6d%61%69%6c%3c%2f%61%3e%27%29%3b'))</script>",
            $this->view->mailTo("me@domain.com", "My email", ['encode' => 'javascript'])
        );
    }

    public function testMailWithOptions(): void
    {
        $this->assertEquals(
            '<a href="mailto:me@example.com?cc=ccaddress%40example.com&amp;bcc=bccaddress%40example.com&amp;body=This%20is%20the%20body%20of%20the%20message.&amp;subject=This%20is%20an%20example%20email">My email</a>',
            $this->view->mailTo("me@example.com", "My email", ['cc' => "ccaddress@example.com", 'bcc' => "bccaddress@example.com", 'subject' => "This is an example email", 'body' => "This is the body of the message."])
        );
    }

    public function testMailToWithImg(): void
    {
        $this->assertEquals(
            '<a href="mailto:feedback@example.com"><img src="/feedback.png"></a>',
            $this->view->mailTo('feedback@example.com', '<img src="/feedback.png">')
        );
    }

    public function testMailToWithHex(): void
    {
        $this->assertEquals(
            "<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">My email</a>",
            $this->view->mailTo("me@domain.com", "My email", ['encode' => "hex"])
        );
        $this->assertEquals(
            "<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">&#109;&#101;&#64;&#100;&#111;&#109;&#97;&#105;&#110;&#46;&#99;&#111;&#109;</a>",
            $this->view->mailTo("me@domain.com", null, ['encode' => "hex"])
        );
    }

    public function testMailToWithReplaceOptions(): void
    {
        $this->assertEquals(
            "<a href=\"mailto:wolfgang@stufenlos.net\">wolfgang(at)stufenlos(dot)net</a>",
            $this->view->mailTo("wolfgang@stufenlos.net", null, ['replaceAt' => "(at)", 'replaceDot' => "(dot)"])
        );
        $this->assertEquals(
            "<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">&#109;&#101;&#40;&#97;&#116;&#41;&#100;&#111;&#109;&#97;&#105;&#110;&#46;&#99;&#111;&#109;</a>",
            $this->view->mailTo("me@domain.com", null, ['encode' => "hex", 'replaceAt' => "(at)"])
        );
        $this->assertEquals(
            "<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">My email</a>",
            $this->view->mailTo("me@domain.com", "My email", ['encode' => "hex", 'replaceAt' => "(at)"])
        );
        $this->assertEquals(
            "<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%6d%65@%64%6f%6d%61%69%6e.%63%6f%6d\">&#109;&#101;&#40;&#97;&#116;&#41;&#100;&#111;&#109;&#97;&#105;&#110;&#40;&#100;&#111;&#116;&#41;&#99;&#111;&#109;</a>",
            $this->view->mailTo("me@domain.com", null, ['encode' => "hex", 'replaceAt' => "(at)", 'replaceDot' => "(dot)"])
        );
    }
}
