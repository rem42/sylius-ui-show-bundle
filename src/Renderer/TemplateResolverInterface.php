<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Renderer;

use Rem42\SyliusUiShow\Exception\TemplateNotFoundException;
use Rem42\SyliusUiShow\Model\ElementInterface;

interface TemplateResolverInterface
{
    /**
     * @throws TemplateNotFoundException
     */
    public function resolve(ElementInterface $element): string;

    /**
     * Template configured for a type ("card", "string", "field_row", "twig_hooks"...).
     *
     * @throws TemplateNotFoundException
     */
    public function getTemplate(string $type): string;

    /**
     * Template wrapping a field with its label.
     */
    public function getFieldRowTemplate(): string;
}
