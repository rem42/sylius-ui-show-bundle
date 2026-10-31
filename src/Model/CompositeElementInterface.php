<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model;

interface CompositeElementInterface extends ElementInterface
{
    public function addChild(ElementInterface $element): static;

    public function withChildren(ElementInterface ...$elements): static;

    /**
     * Adds fields whose type will be guessed from the resource metadata.
     */
    public function add(string ...$names): static;

    public function hasChild(string $name): bool;

    public function getChild(string $name): ?ElementInterface;

    public function removeChild(string $name): static;

    public function replaceChild(string $name, ElementInterface $element): static;

    /**
     * Returns enabled children sorted by priority (highest first).
     *
     * @return array<string, ElementInterface>
     */
    public function getChildren(): array;

    /**
     * Returns every child, including the disabled ones, in insertion order.
     *
     * @return array<string, ElementInterface>
     */
    public function getAllChildren(): array;

    /**
     * Finds a descendant using a dot-separated path of names (e.g. "general.details.name").
     */
    public function find(string $path): ?ElementInterface;
}
