<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

class BooleanField extends AbstractField
{
    public function getType(): string
    {
        return 'boolean';
    }

    public function setBadge(bool $badge): static
    {
        return $this->setOption('badge', $badge);
    }

    public function hasBadge(): bool
    {
        return false !== $this->getOption('badge', true);
    }
}
