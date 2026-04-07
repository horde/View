<?php

declare(strict_types=1);

/**
 * Copyright 2007-2026 Maintainable Software, LLC
 * Copyright 2008-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 */

namespace Horde\View\Mock;

use Horde_View_Helper_Url;

class FormTagUrlHelper extends Horde_View_Helper_Url
{
    public function urlFor($first = [], $second = [])
    {
        return $first ? parent::urlFor($first, $second) : 'http://www.example.com';
    }
}
