<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model;

interface ElementInterface
{
    /**
     * Unique name of the element among its siblings.
     */
    public function getName(): string;

    /**
     * Type used to resolve the Twig template (see "rem42_sylius_ui_show.templates").
     */
    public function getType(): string;

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array;

    public function getOption(string $key, mixed $default = null): mixed;

    public function setOption(string $key, mixed $value): static;

    /**
     * @param array<string, mixed> $options
     */
    public function addOptions(array $options): static;

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array;

    public function setAttribute(string $key, mixed $value): static;

    public function addClass(string ...$classes): static;

    public function removeClass(string ...$classes): static;

    /**
     * Returns the label (translation key or plain text), false to hide it, or null to let the renderer generate one.
     */
    public function getLabel(): string|false|null;

    public function setLabel(string|false|null $label): static;

    public function getTranslationDomain(): ?string;

    public function setTranslationDomain(?string $translationDomain): static;

    /**
     * Custom template overriding the one configured for the element type.
     */
    public function getTemplate(): ?string;

    public function setTemplate(?string $template): static;

    public function getPriority(): int;

    public function setPriority(int $priority): static;

    public function isEnabled(): bool;

    public function setEnabled(bool $enabled): static;
}
