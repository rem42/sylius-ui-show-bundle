<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;
use Rem42\SyliusUiShow\Model\Color;

class BadgeField extends AbstractField
{
    public function getType(): string
    {
        return 'badge';
    }

    /**
     * @param array<int|string, Color|string> $colorMapping value => color
     */
    public function setColorMapping(array $colorMapping): static
    {
        return $this->setOption('color_mapping', $colorMapping);
    }

    /**
     * @return array<int|string, string>
     */
    public function getColorMapping(): array
    {
        return $this->getColorMapOption('color_mapping');
    }

    public function setDefaultColor(Color|string $color): static
    {
        return $this->setOption('default_color', $color);
    }

    public function getDefaultColor(): string
    {
        return $this->getColorOption('default_color', Color::Secondary) ?? Color::Secondary->value;
    }

    public function setOutline(bool $outline = true): static
    {
        return $this->setOption('outline', $outline);
    }

    public function isOutline(): bool
    {
        return true === $this->getOption('outline', false);
    }

    public function setPill(bool $pill = true): static
    {
        return $this->setOption('pill', $pill);
    }

    public function isPill(): bool
    {
        return true === $this->getOption('pill', false);
    }
}
