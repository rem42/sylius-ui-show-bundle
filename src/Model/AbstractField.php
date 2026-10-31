<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model;

use Rem42\SyliusUiShow\Exception\InvalidShowException;

abstract class AbstractField extends AbstractElement implements FieldInterface
{
    public function getPath(): string
    {
        $path = $this->getOption('path');

        return \is_string($path) && '' !== $path ? $path : $this->getName();
    }

    public function setPath(string $path): static
    {
        return $this->setOption('path', $path);
    }

    public function getEmptyValue(): string
    {
        $emptyValue = $this->getOption('empty_value', '-');

        return \is_scalar($emptyValue) ? (string) $emptyValue : '-';
    }

    public function setEmptyValue(string $emptyValue): static
    {
        return $this->setOption('empty_value', $emptyValue);
    }

    /**
     * Reads an option holding a {@see Color}, as its value.
     *
     * @throws InvalidShowException when the color is not a {@see Color}
     */
    protected function getColorOption(string $key, ?Color $default = null): ?string
    {
        $color = $this->getOption($key);

        return $color instanceof Color || \is_string($color) ? Color::normalize($color) : $default?->value;
    }

    /**
     * Reads an option holding a map of {@see Color}, as their values.
     *
     * @return array<array-key, string>
     *
     * @throws InvalidShowException when a color is not a {@see Color}
     */
    protected function getColorMapOption(string $key): array
    {
        $option = $this->getOption($key);

        if (!\is_array($option)) {
            return [];
        }

        $colors = [];

        foreach ($option as $value => $color) {
            if ($color instanceof Color || \is_string($color)) {
                $colors[$value] = Color::normalize($color);
            }
        }

        return $colors;
    }
}
