<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Renderer;

use Rem42\SyliusUiShow\Accessor\ValueAccessorInterface;
use Rem42\SyliusUiShow\Model\ElementInterface;
use Rem42\SyliusUiShow\Model\FieldInterface;
use Rem42\SyliusUiShow\Model\Layout\Template;
use Twig\Environment;
use Twig\Markup;

final class TwigShowRenderer implements ShowRendererInterface
{
    public function __construct(
        private readonly Environment $twig,
        private readonly TemplateResolverInterface $templateResolver,
        private readonly ValueAccessorInterface $valueAccessor,
    ) {}

    public function render(ElementInterface $element, mixed $resource, array $options = []): string
    {
        if (!$element->isEnabled()) {
            return '';
        }

        $context = [
            'element' => $element,
            'resource' => $resource,
            'options' => $options,
        ];

        if ($element instanceof Template) {
            $context = array_merge($element->getContext(), $context);
        }

        if (!$element instanceof FieldInterface) {
            return $this->twig->render($this->templateResolver->resolve($element), $context);
        }

        $context['field'] = $element;
        $context['value'] = $this->valueAccessor->getValue($resource, $element->getPath());

        $content = $this->twig->render($this->templateResolver->resolve($element), $context);

        if (false === ($options['render_label'] ?? true) || false === $element->getLabel()) {
            return $content;
        }

        return $this->twig->render($this->templateResolver->getFieldRowTemplate(), array_merge($context, [
            'content' => new Markup($content, $this->twig->getCharset()),
        ]));
    }
}
