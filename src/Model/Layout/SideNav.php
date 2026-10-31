<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * A vertical navigation (list-group) switching between {@see SideNavItem} panes.
 */
class SideNav extends CompositeElement
{
    public function getType(): string
    {
        return 'side_nav';
    }

    public function addItem(SideNavItem|SideNavDivider $item): static
    {
        return $this->addChild($item);
    }
}
