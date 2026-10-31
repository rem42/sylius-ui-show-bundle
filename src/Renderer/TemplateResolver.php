<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Renderer;

use Rem42\SyliusUiShow\Exception\TemplateNotFoundException;
use Rem42\SyliusUiShow\Model\ElementInterface;

final class TemplateResolver implements TemplateResolverInterface
{
    public const FIELD_ROW = 'field_row';

    /**
     * @param array<string, string> $templates element type => template
     */
    public function __construct(
        private readonly array $templates,
    ) {}

    public function resolve(ElementInterface $element): string
    {
        return $element->getTemplate()
            ?? $this->templates[$element->getType()]
            ?? throw TemplateNotFoundException::forElement($element);
    }

    public function getTemplate(string $type): string
    {
        return $this->templates[$type] ?? throw TemplateNotFoundException::forType($type);
    }

    public function getFieldRowTemplate(): string
    {
        return $this->getTemplate(self::FIELD_ROW);
    }
}
