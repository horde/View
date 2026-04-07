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

use Horde_View_Helper_Form_Builder;

class Builder extends Horde_View_Helper_Form_Builder
{
    public function foo()
    {
        return '<foo />';
    }
}
