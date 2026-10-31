<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * Renders its {@see Tab} children as Bootstrap tabs inside a card.
 */
class Tabs extends CompositeElement
{
    public function getType(): string
    {
        return 'tabs';
    }

    public function addTab(Tab $tab): static
    {
        return $this->addChild($tab);
    }
}
