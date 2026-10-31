<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

/**
 * Placeholder created by CompositeElement::add(): replaced at build time by a field guessed from the resource metadata.
 */
final class AutoField extends AbstractField
{
    public function getType(): string
    {
        return 'auto';
    }
}
