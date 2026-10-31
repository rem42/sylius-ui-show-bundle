<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

/**
 * Renders a CSS icon class (e.g. "ti ti-star"). Raw SVG output must be explicitly allowed as it is not escaped.
 */
class IconField extends AbstractField
{
    public function getType(): string
    {
        return 'icon';
    }

    public function setAllowSvg(bool $allowSvg = true): static
    {
        return $this->setOption('allow_svg', $allowSvg);
    }

    public function isSvgAllowed(): bool
    {
        return true === $this->getOption('allow_svg', false);
    }
}
