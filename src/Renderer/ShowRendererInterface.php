<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Renderer;

use Rem42\SyliusUiShow\Model\ElementInterface;

interface ShowRendererInterface
{
    /**
     * Renders an element (a whole show, a layout or a field) for the given resource.
     *
     * Supported options:
     *  - render_label (bool, default true): wraps fields with their label.
     *
     * @param array<string, mixed> $options
     */
    public function render(ElementInterface $element, mixed $resource, array $options = []): string;
}
