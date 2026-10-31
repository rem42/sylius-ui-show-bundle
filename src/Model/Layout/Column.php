<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * A responsive column of a 12 columns grid, to be used inside a {@see Section}.
 */
class Column extends CompositeElement
{
    /**
     * @param string|int<1, 12>    $nameOrWidth a name, or directly the width (a unique name is then generated)
     * @param array<string, mixed> $options
     */
    public static function create(string|int $nameOrWidth, array $options = []): static
    {
        if (\is_int($nameOrWidth)) {
            $options['width'] = $nameOrWidth;
            $nameOrWidth = uniqid('column_', false);
        }

        return new static($nameOrWidth, $options);
    }

    public function getType(): string
    {
        return 'column';
    }

    /**
     * @param int<1, 12> $width
     */
    public function setWidth(int $width): static
    {
        return $this->setOption('width', $width);
    }

    public function getWidth(): int
    {
        $width = $this->getOption('width', 12);

        return \is_int($width) ? max(1, min(12, $width)) : 12;
    }

    /**
     * Breakpoint from which the width applies (below it the column takes the full width).
     * Use an empty string to apply the width on every screen size.
     */
    public function setBreakpoint(string $breakpoint): static
    {
        return $this->setOption('breakpoint', $breakpoint);
    }

    public function getBreakpoint(): string
    {
        $breakpoint = $this->getOption('breakpoint', 'md');

        return \is_string($breakpoint) ? $breakpoint : 'md';
    }
}
