<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\AbstractElement;

/**
 * A labelled separator between {@see SideNavItem} entries.
 */
class SideNavDivider extends AbstractElement
{
    public function getType(): string
    {
        return 'side_nav_divider';
    }
}
