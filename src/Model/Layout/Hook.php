<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\AbstractElement;

/**
 * Renders a Sylius Twig Hook (requires sylius/twig-hooks). Hookables receive "resource" and "element" in their context.
 */
class Hook extends AbstractElement
{
    public function getType(): string
    {
        return 'hook';
    }

    public function setHookName(string $hookName): static
    {
        return $this->setOption('hook', $hookName);
    }

    /**
     * Defaults to the element name.
     */
    public function getHookName(): string
    {
        $hook = $this->getOption('hook');

        return \is_string($hook) && '' !== $hook ? $hook : $this->getName();
    }
}
