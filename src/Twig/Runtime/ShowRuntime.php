<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Twig\Runtime;

use Rem42\SyliusUiShow\Accessor\ValueAccessorInterface;
use Rem42\SyliusUiShow\Factory\ShowFactoryInterface;
use Rem42\SyliusUiShow\Model\ElementInterface;
use Rem42\SyliusUiShow\Renderer\LabelGenerator;
use Rem42\SyliusUiShow\Renderer\ShowRendererInterface;
use Rem42\SyliusUiShow\Renderer\TemplateResolverInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ShowRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ShowFactoryInterface $showFactory,
        private readonly ShowRendererInterface $renderer,
        private readonly ValueAccessorInterface $valueAccessor,
        private readonly LabelGenerator $labelGenerator,
        private readonly TemplateResolverInterface $templateResolver,
    ) {}

    /**
     * {{ rem42_sylius_ui_show(resource) }} or {{ rem42_sylius_ui_show(resource, 'app_product') }}.
     *
     * @param array<string, mixed> $options
     */
    public function renderShow(object $resource, ?string $name = null, array $options = []): string
    {
        return $this->renderer->render($this->showFactory->create($resource, $name), $resource, $options);
    }

    public function hasShow(mixed $resource, ?string $name = null): bool
    {
        return \is_object($resource) && $this->showFactory->supports($resource, $name);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function renderElement(ElementInterface $element, mixed $resource, array $options = []): string
    {
        return $this->renderer->render($element, $resource, $options);
    }

    public function getValue(mixed $resource, string $path): mixed
    {
        return $this->valueAccessor->getValue($resource, $path);
    }

    public function getLabel(ElementInterface $element): string
    {
        return $this->labelGenerator->generate($element);
    }

    /**
     * Template configured for a type, in the current theme: {% include rem42_sylius_ui_show_template('twig_hooks') %}.
     */
    public function getTemplate(string $type): string
    {
        return $this->templateResolver->getTemplate($type);
    }

    /**
     * Renders the HTML attributes of an element, merged with default ones (classes are concatenated).
     *
     * @param array<string, mixed> $defaults
     */
    public function renderAttributes(ElementInterface $element, array $defaults = []): string
    {
        $attributes = array_merge($defaults, $element->getAttributes());

        $classes = trim(sprintf('%s %s', $this->stringify($defaults['class'] ?? ''), $this->stringify($element->getAttributes()['class'] ?? '')));
        unset($attributes['class']);

        if ('' !== $classes) {
            $attributes = ['class' => $classes] + $attributes;
        }

        $html = '';

        foreach ($attributes as $name => $value) {
            if (null === $value || false === $value) {
                continue;
            }

            $name = htmlspecialchars((string) $name, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');

            $html .= true === $value
                ? ' ' . $name
                : sprintf(' %s="%s"', $name, htmlspecialchars($this->stringify($value), \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8'));
        }

        return $html;
    }

    /**
     * Converts any value to a displayable string: enums, dates, booleans, iterables and stringable objects are supported.
     */
    public function stringify(mixed $value, string $separator = ', '): string
    {
        return match (true) {
            null === $value => '',
            \is_bool($value) => $value ? '1' : '0',
            \is_scalar($value), $value instanceof \Stringable => (string) $value,
            $value instanceof \BackedEnum => (string) $value->value,
            $value instanceof \UnitEnum => $value->name,
            $value instanceof \DateTimeInterface => $value->format(\DateTimeInterface::ATOM),
            is_iterable($value) => implode($separator, array_map(fn (mixed $item): string => $this->stringify($item, $separator), iterator_to_array($value, false))),
            default => get_debug_type($value),
        };
    }
}
