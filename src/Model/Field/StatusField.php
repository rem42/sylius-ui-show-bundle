<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;
use Rem42\SyliusUiShow\Model\Color;

class StatusField extends AbstractField
{
    public function getType(): string
    {
        return 'status';
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

    public function setAnimated(bool $animated = true): static
    {
        return $this->setOption('animated', $animated);
    }

    public function isAnimated(): bool
    {
        return true === $this->getOption('animated', false);
    }
}
