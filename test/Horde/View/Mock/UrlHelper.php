<?php
/**
 * Copyright 2007-2008 Maintainable Software, LLC
 * Copyright 2008-2017 Horde LLC (http://www.horde.org/)
 *
 * @author     Mike Naberezny <mike@maintainable.com>
 * @author     Derek DeVries <derek@maintainable.com>
 * @author     Chuck Hagenbuch <chuck@horde.org>
 * @license    http://www.horde.org/licenses/bsd
 * @category   Horde
 * @package    View
 * @subpackage UnitTests
 */
namespace Horde\View\Mock;
use \Horde_View_Helper_Base;

/**
 * Mock for an UrlHelper
 */
class UrlHelper extends Horde_View_Helper_Base
{
    public function urlFor($options)
    {
        return 'http://www.example.com';
    }
}
