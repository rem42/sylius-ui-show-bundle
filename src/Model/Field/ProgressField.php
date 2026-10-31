<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;
use Rem42\SyliusUiShow\Model\Color;

class ProgressField extends AbstractField
{
    public function getType(): string
    {
        return 'progress';
    }

    public function setMax(int|float $max): static
    {
        return $this->setOption('max', $max);
    }

    public function getMax(): int|float
    {
        $max = $this->getOption('max', 100);

        return \is_int($max) || \is_float($max) ? $max : 100;
    }

    public function setColor(Color|string|null $color): static
    {
        return $this->setOption('color', $color);
    }

    public function getColor(): ?string
    {
        return $this->getColorOption('color');
    }
}
