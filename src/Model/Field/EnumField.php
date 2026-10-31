<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;
use Rem42\SyliusUiShow\Model\Color;

class EnumField extends AbstractField
{
    public function getType(): string
    {
        return 'enum';
    }

    /**
     * Maps a value (backed value or case name) to a label.
     *
     * @param array<int|string, string> $choices
     */
    public function setChoices(array $choices): static
    {
        return $this->setOption('choices', $choices);
    }

    /**
     * @return array<int|string, string>
     */
    public function getChoices(): array
    {
        return $this->getStringMapOption('choices');
    }

    public function setTranslatable(bool $translatable = true): static
    {
        return $this->setOption('translatable', $translatable);
    }

    public function isTranslatable(): bool
    {
        return true === $this->getOption('translatable', false);
    }

    /**
     * Displays the value in a badge. Pass a value => color map to colorize each value.
     *
     * @param bool|array<int|string, Color|string> $badge
     */
    public function setBadge(bool|array $badge): static
    {
        return $this->setOption('badge', $badge);
    }

    /**
     * @return bool|array<int|string, string>
     */
    public function getBadge(): bool|array
    {
        $badge = $this->getOption('badge', false);

        return \is_array($badge) ? $this->getColorMapOption('badge') : true === $badge;
    }
}
