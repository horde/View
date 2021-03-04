<?php
/**
 * Copyright 2006-2017 Horde LLC (http://www.horde.org/)
 *
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @category   Horde
 * @package    View
 * @subpackage UnitTests
 */
namespace Horde\View;
use \PHPUnit\Framework\TestCase;
use \Horde_View;
use \Horde_View_Interface;

/**
 * @group      view
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @category   Horde
 * @package    View
 * @subpackage UnitTests
 */
class InterfaceTest extends TestCase {

    public function testViewInterface()
    {
        eval('class Test_View extends Horde_View implements Horde_View_Interface {};');
        $view = new \Test_View;
        $this->assertInstanceOf(Horde_View::class, $view);
        $this->assertInstanceOf(Horde_View_Interface::class, $view);
    }
}
