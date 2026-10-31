<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Renderer;

use Rem42\SyliusUiShow\Model\ElementInterface;

/**
 * Generates the translation key of elements without an explicit label: "<prefix><snake_cased_name>".
 */
final class LabelGenerator
{
    public function __construct(
        private readonly string $prefix = 'sylius.ui.',
    ) {}

    public function generate(ElementInterface $element): string
    {
        $label = $element->getLabel();

        if (false === $label) {
            return '';
        }

        if (null !== $label) {
            return $label;
        }

        $name = str_replace(['.', '-', ' '], '_', $element->getName());
        $name = strtolower((string) preg_replace('/(?<=[a-z0-9])([A-Z])/', '_$1', $name));

        return $this->prefix . $name;
    }
}
