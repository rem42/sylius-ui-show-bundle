<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

class UrlField extends AbstractField
{
    public function getType(): string
    {
        return 'url';
    }

    public function setNewTab(bool $newTab = true): static
    {
        return $this->setOption('new_tab', $newTab);
    }

    public function isNewTab(): bool
    {
        return true === $this->getOption('new_tab', false);
    }

    /**
     * Property path of the resource used as link text (defaults to the URL itself).
     */
    public function setLabelProperty(?string $labelProperty): static
    {
        return $this->setOption('label_property', $labelProperty);
    }

    public function getLabelProperty(): ?string
    {
        $property = $this->getOption('label_property');

        return \is_string($property) ? $property : null;
    }
}
