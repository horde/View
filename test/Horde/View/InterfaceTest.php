<?php
/**
 * Copyright 2006-2017 Horde LLC (http://www.horde.org/)
 *
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @category   Horde
 * @package    View
 * @subpackage UnitTests
 */

/**
 * @group      view
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @category   Horde
 * @package    View
 * @subpackage UnitTests
 */
class Horde_View_InterfaceTest extends Horde_Test_Case {

    public function testViewInterface()
    {
        $this->expectNotToPerformAssertions();
        eval('class Test_View extends Horde_View implements Horde_View_Interface {};');
    }

}
