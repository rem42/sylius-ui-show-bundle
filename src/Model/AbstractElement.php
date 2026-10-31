<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model;

abstract class AbstractElement implements ElementInterface
{
    /** @var array<string, mixed> */
    private array $options = [];

    /** @var array<string, mixed> */
    private array $attributes = [];

    private string|false|null $label = null;

    private ?string $translationDomain = null;

    private ?string $template = null;

    private int $priority = 0;

    private bool $enabled = true;

    /**
     * @param array<string, mixed> $options
     */
    final public function __construct(
        private readonly string $name,
        array $options = [],
    ) {
        $this->addOptions(static::getDefaultOptions());
        $this->addOptions($options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function create(string $name, array $options = []): static
    {
        return new static($name, $options);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $key, mixed $default = null): mixed
    {
        return \array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }

    public function hasOption(string $key): bool
    {
        return \array_key_exists($key, $this->options);
    }

    /**
     * Generic accessor used by templates: {{ element.option('foo', 'default') }}.
     */
    public function option(string $key, mixed $default = null): mixed
    {
        return $this->getOption($key, $default);
    }

    public function setOption(string $key, mixed $value): static
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function addOptions(array $options): static
    {
        foreach ($options as $key => $value) {
            $this->setOption((string) $key, $value);
        }

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttribute(string $key, mixed $value): static
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function addClass(string ...$classes): static
    {
        $current = $this->getClasses();

        foreach ($classes as $class) {
            foreach (preg_split('/\s+/', trim($class)) ?: [] as $item) {
                if ('' !== $item && !\in_array($item, $current, true)) {
                    $current[] = $item;
                }
            }
        }

        $this->attributes['class'] = implode(' ', $current);

        return $this;
    }

    public function removeClass(string ...$classes): static
    {
        $this->attributes['class'] = implode(' ', array_diff($this->getClasses(), $classes));

        return $this;
    }

    public function getLabel(): string|false|null
    {
        return $this->label;
    }

    public function setLabel(string|false|null $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getTranslationDomain(): ?string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(?string $translationDomain): static
    {
        $this->translationDomain = $translationDomain;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Copies the presentation state (label, attributes, template...) of another element onto this one.
     */
    public function mergeStateFrom(self $element): static
    {
        $this->addOptions($element->getOptions());
        $this->attributes = array_merge($this->attributes, $element->getAttributes());
        $this->label = $element->getLabel();
        $this->translationDomain = $element->getTranslationDomain();
        $this->template = $element->getTemplate() ?? $this->template;
        $this->priority = $element->getPriority();
        $this->enabled = $element->isEnabled();

        return $this;
    }

    /**
     * Reads an option holding a map of strings, ignoring invalid entries.
     *
     * @return array<array-key, string>
     */
    protected function getStringMapOption(string $key): array
    {
        $option = $this->getOption($key);

        return \is_array($option) ? array_filter($option, '\is_string') : [];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function getDefaultOptions(): array
    {
        return [];
    }

    /**
     * @return list<string>
     */
    private function getClasses(): array
    {
        $class = $this->attributes['class'] ?? '';

        return array_values(array_filter(
            preg_split('/\s+/', \is_string($class) ? trim($class) : '') ?: [],
            static fn (string $item): bool => '' !== $item,
        ));
    }
}
