<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * A single pane of a {@see SideNav} element.
 */
class SideNavItem extends CompositeElement
{
    public function getType(): string
    {
        return 'side_nav_item';
    }
}
