<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * Renders its fields using the Tabler "datagrid" component (title above value, responsive grid).
 */
class Datagrid extends CompositeElement
{
    public function getType(): string
    {
        return 'datagrid';
    }
}
