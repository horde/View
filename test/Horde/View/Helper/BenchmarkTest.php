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

use Horde_Log;
use Horde_Log_Handler_Mock;
use Horde_Log_Logger;
use Horde_View;
use Horde_View_Helper_Benchmark;
use Horde_View_Helper_Benchmark_Timer as Timer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('view')]
#[CoversClass(Horde_View_Helper_Benchmark::class)]
#[CoversClass(Timer::class)]
class BenchmarkTest extends TestCase
{
    private Horde_View $view;
    private Horde_Log_Handler_Mock $mock;

    public function setUp(): void
    {
        $this->view = new Horde_View();
        $this->view->addHelper(new Horde_View_Helper_Benchmark($this->view));

        $log = new Horde_Log_Logger($this->mock = new Horde_Log_Handler_Mock());
        $this->view->logger = $log;
    }

    public function testWithoutLogger(): void
    {
        $this->view = new Horde_View();
        $this->view->addHelper(new Horde_View_Helper_Benchmark($this->view));

        $bench = $this->view->benchmark();
        $ret = $bench->end();
        $this->assertInstanceOf(Timer::class, $bench);
        $this->assertNull($ret);
    }

    public function testDefaults(): void
    {
        $bench = $this->view->benchmark();
        $bench->end();
        $this->assertCount(1, $this->mock->events);
        $this->assertLastLogged();
    }

    public function testWithMessage(): void
    {
        $bench = $this->view->benchmark('test_run');
        $bench->end();
        $this->assertCount(1, $this->mock->events);
        $this->assertLastLogged('test_run');
    }

    public function testWithMessageAndLevelAsString(): void
    {
        $bench = $this->view->benchmark('debug_run', 'debug');
        $bench->end();
        $this->assertCount(1, $this->mock->events);
        $this->assertLastLogged('debug_run', 'debug');
    }

    public function testWithMessageAndLevelAsInteger(): void
    {
        $bench = $this->view->benchmark('debug_run', Horde_Log::DEBUG);
        $bench->end();
        $this->assertCount(1, $this->mock->events);
        $this->assertLastLogged('debug_run', 'debug');
    }

    private function assertLastLogged(string $message = 'Benchmarking', string $level = 'info'): void
    {
        $last = end($this->mock->events);
        $this->assertEquals(strtoupper($level), $last['levelName']);
        $this->assertMatchesRegularExpression("/^$message \(.*\)$/", $last['message']);
    }
}
