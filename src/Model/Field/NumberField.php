<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

class NumberField extends AbstractField
{
    public function getType(): string
    {
        return 'number';
    }

    public function setDecimals(int $decimals): static
    {
        return $this->setOption('decimals', $decimals);
    }

    public function getDecimals(): ?int
    {
        $decimals = $this->getOption('decimals');

        return \is_int($decimals) ? $decimals : null;
    }

    public function setPrefix(?string $prefix): static
    {
        return $this->setOption('prefix', $prefix);
    }

    public function getPrefix(): ?string
    {
        $prefix = $this->getOption('prefix');

        return \is_string($prefix) ? $prefix : null;
    }

    public function setSuffix(?string $suffix): static
    {
        return $this->setOption('suffix', $suffix);
    }

    public function getSuffix(): ?string
    {
        $suffix = $this->getOption('suffix');

        return \is_string($suffix) ? $suffix : null;
    }
}
