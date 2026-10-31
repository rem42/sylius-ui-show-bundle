<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * A Bootstrap row: use it to lay out columns or cards side by side.
 */
class Section extends CompositeElement
{
    public function getType(): string
    {
        return 'section';
    }
}
