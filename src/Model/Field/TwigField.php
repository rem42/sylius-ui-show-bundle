<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

/**
 * Renders the value with a custom template (see setTemplate()). The template receives "value", "field", "resource" and "options".
 */
class TwigField extends AbstractField
{
    public function getType(): string
    {
        return 'twig';
    }
}
