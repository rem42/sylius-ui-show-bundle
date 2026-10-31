<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * Renders a custom Twig template. The template receives "element", "resource", "options" and the "context" variables.
 */
class Template extends CompositeElement
{
    public function getType(): string
    {
        return 'template';
    }

    /**
     * @param array<string, mixed> $context
     */
    public function setContext(array $context): static
    {
        return $this->setOption('context', $context);
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        $context = [];

        foreach ((array) $this->getOption('context', []) as $key => $value) {
            $context[(string) $key] = $value;
        }

        return $context;
    }
}
