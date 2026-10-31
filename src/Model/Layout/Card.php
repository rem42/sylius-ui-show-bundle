<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * A card with an optional header. Children are rendered in its body.
 */
class Card extends CompositeElement
{
    public function getType(): string
    {
        return 'card';
    }

    public function setHeader(bool $header): static
    {
        return $this->setOption('header', $header);
    }

    public function hasHeader(): bool
    {
        return false !== $this->getOption('header', true) && false !== $this->getLabel();
    }

    /**
     * Renders the body edge to edge, without padding (e.g. around a {@see DetailsTable} or a table).
     */
    public function setFlush(bool $flush = true): static
    {
        return $this->setOption('flush', $flush);
    }

    public function isFlush(): bool
    {
        return true === $this->getOption('flush', false);
    }

    /**
     * Additional classes of the body, specific to the theme.
     */
    public function setBodyClass(string $class): static
    {
        return $this->setOption('body_class', $class);
    }

    public function getBodyClass(): string
    {
        $class = $this->getOption('body_class', '');

        return \is_string($class) ? $class : '';
    }
}
