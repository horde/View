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
use Horde_View_Helper_Date;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Date::class)]
class DateTest extends TestCase
{
    private Horde_View_Helper_Date $helper;

    public function setUp(): void
    {
        $this->helper = new Horde_View_Helper_Date(new Horde_View());
    }

    public function testDistanceInWords(): void
    {
        $from = mktime(21, 45, 0, 6, 6, 2004);

        // 0..1 with $includeSeconds
        $this->assertEquals(
            'less than 5 seconds',
            $this->helper->distanceOfTimeInWords($from, $from + 0, true)
        );
        $this->assertEquals(
            'less than 5 seconds',
            $this->helper->distanceOfTimeInWords($from, $from + 4, true)
        );
        $this->assertEquals(
            'less than 10 seconds',
            $this->helper->distanceOfTimeInWords($from, $from + 5, true)
        );
        $this->assertEquals(
            'less than 10 seconds',
            $this->helper->distanceOfTimeInWords($from, $from + 9, true)
        );
        $this->assertEquals(
            'less than 20 seconds',
            $this->helper->distanceOfTimeInWords($from, $from + 10, true)
        );
        $this->assertEquals(
            'less than 20 seconds',
            $this->helper->distanceOfTimeInWords($from, $from + 19, true)
        );
        $this->assertEquals(
            'half a minute',
            $this->helper->distanceOfTimeInWords($from, $from + 20, true)
        );
        $this->assertEquals(
            'half a minute',
            $this->helper->distanceOfTimeInWords($from, $from + 39, true)
        );
        $this->assertEquals(
            'less than a minute',
            $this->helper->distanceOfTimeInWords($from, $from + 40, true)
        );
        $this->assertEquals(
            'less than a minute',
            $this->helper->distanceOfTimeInWords($from, $from + 59, true)
        );
        $this->assertEquals(
            '1 minute',
            $this->helper->distanceOfTimeInWords($from, $from + 60, true)
        );
        $this->assertEquals(
            '1 minute',
            $this->helper->distanceOfTimeInWords($from, $from + 89, true)
        );

        // First case 0..1
        $this->assertEquals(
            'less than a minute',
            $this->helper->distanceOfTimeInWords($from, $from + 0)
        );
        $this->assertEquals(
            'less than a minute',
            $this->helper->distanceOfTimeInWords($from, $from + 29)
        );
        $this->assertEquals(
            'less than a minute',
            $this->helper->distanceOfTimeInWords($from, $from + 30)
        );
        $this->assertEquals(
            '1 minute',
            $this->helper->distanceOfTimeInWords($from, $from + (1 * 60) + 29)
        );

        // 2..44
        $this->assertEquals(
            '2 minutes',
            $this->helper->distanceOfTimeInWords($from, $from + (2 * 60) + 30)
        );
        $this->assertEquals(
            '44 minutes',
            $this->helper->distanceOfTimeInWords($from, $from + (44 * 60) + 29)
        );

        // 45..89
        $this->assertEquals(
            'about 1 hour',
            $this->helper->distanceOfTimeInWords($from, $from + (45 * 60))
        );
        $this->assertEquals(
            'about 1 hour',
            $this->helper->distanceOfTimeInWords($from, $from + (89 * 60) + 29)
        );

        // 90..1439
        $this->assertEquals(
            'about 2 hours',
            $this->helper->distanceOfTimeInWords($from, $from + (90 * 60))
        );
        $this->assertEquals(
            'about 24 hours',
            $this->helper->distanceOfTimeInWords($from, $from + (23 * 3600) + (59 * 60) + 29)
        );

        // 2880..43199
        $this->assertEquals(
            '2 days',
            $this->helper->distanceOfTimeInWords($from, $from + (47 * 3600) + (60 * 60))
        );
        $this->assertEquals(
            '29 days',
            $this->helper->distanceOfTimeInWords($from, $from + (29 * 86400) + (23 * 3600) + (59 * 60) + 29)
        );

        // 43200..86399
        $this->assertEquals(
            'about 1 month',
            $this->helper->distanceOfTimeInWords($from, $from + (29 * 86400) + (23 * 3600) + (60 * 60))
        );
        $this->assertEquals(
            'about 1 month',
            $this->helper->distanceOfTimeInWords($from, $from + (59 * 86400) + (23 * 3600) + (59 * 60) + 29)
        );

        // 86400..525599
        $this->assertEquals(
            '2 months',
            $this->helper->distanceOfTimeInWords($from, $from + (59 * 86400) + (23 * 3600) + (60 * 60))
        );

        $this->assertEquals(
            '12 months',
            $this->helper->distanceOfTimeInWords($from, $from + (1 * 31557600) - 31)
        );

        // 525960..1051919
        $this->assertEquals(
            'about 1 year',
            $this->helper->distanceOfTimeInWords($from, $from + (1 * 31557600))
        );
        $this->assertEquals(
            'about 1 year',
            $this->helper->distanceOfTimeInWords($from, $from + (2 * 31557600) - 31)
        );

        // > 1051920
        $this->assertEquals(
            'over 2 years',
            $this->helper->distanceOfTimeInWords($from, $from + (2 * 31557600))
        );
        $this->assertEquals(
            'over 10 years',
            $this->helper->distanceOfTimeInWords($from, $from + (10 * 31557600))
        );

        // test to < from
        $this->assertEquals(
            'about 4 hours',
            $this->helper->distanceOfTimeInWords($from + (4 * 3600), $from)
        );
        $this->assertEquals(
            'less than 20 seconds',
            $this->helper->distanceOfTimeInWords($from + 19, $from, true)
        );
    }

    public function testDistanceInWordsWithIntegers(): void
    {
        $this->assertEquals(
            'less than a minute',
            $this->helper->distanceOfTimeInWords(59)
        );
        $this->assertEquals('about 1 hour',
            $this->helper->distanceOfTimeInWords(60 * 60));
        $this->assertEquals('less than a minute',
            $this->helper->distanceOfTimeInWords(0, 59));
        $this->assertEquals('about 1 hour',
            $this->helper->distanceOfTimeInWords(60 * 60, 0));
    }

    public function testTimeAgoInWords(): void
    {
        // timeAgoInWords wraps distanceOfTimeInWords with time() as second arg
        // We can verify it returns a string for a recent timestamp
        $result = $this->helper->timeAgoInWords(time() - 120);
        $this->assertEquals('2 minutes', $result);
    }
}
