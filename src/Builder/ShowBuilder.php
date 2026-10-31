<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Builder;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * Root element of a show, handed to every show definition.
 */
final class ShowBuilder extends CompositeElement
{
    public function getType(): string
    {
        return 'show';
    }
}
