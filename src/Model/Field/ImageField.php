<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

class ImageField extends AbstractField
{
    public const SHAPE_SQUARE = 'square';

    public const SHAPE_ROUNDED = 'rounded';

    public const SHAPE_CIRCLE = 'circle';

    public function getType(): string
    {
        return 'image';
    }

    public function setShape(string $shape): static
    {
        return $this->setOption('shape', $shape);
    }

    public function getShape(): string
    {
        $shape = $this->getOption('shape', self::SHAPE_SQUARE);

        return \is_string($shape) ? $shape : self::SHAPE_SQUARE;
    }

    /**
     * Tabler avatar size (xs, sm, md, lg, xl), null for the default size.
     */
    public function setSize(?string $size): static
    {
        return $this->setOption('size', $size);
    }

    public function getSize(): ?string
    {
        $size = $this->getOption('size');

        return \is_string($size) ? $size : null;
    }

    /**
     * Wraps the image in a link opening the full-size picture.
     */
    public function setLightbox(bool $lightbox = true): static
    {
        return $this->setOption('lightbox', $lightbox);
    }

    public function hasLightbox(): bool
    {
        return true === $this->getOption('lightbox', false);
    }

    /**
     * Prefix prepended to the value (e.g. a media base path or a CDN host).
     */
    public function setBasePath(?string $basePath): static
    {
        return $this->setOption('base_path', $basePath);
    }

    public function getBasePath(): ?string
    {
        $basePath = $this->getOption('base_path');

        return \is_string($basePath) ? $basePath : null;
    }
}
