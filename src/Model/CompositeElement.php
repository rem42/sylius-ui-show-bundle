<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model;

use Rem42\SyliusUiShow\Model\Field\AutoField;

abstract class CompositeElement extends AbstractElement implements CompositeElementInterface
{
    /** @var array<string, ElementInterface> */
    private array $children = [];

    public function addChild(ElementInterface $element): static
    {
        $this->children[$element->getName()] = $element;

        return $this;
    }

    public function withChildren(ElementInterface ...$elements): static
    {
        foreach ($elements as $element) {
            $this->addChild($element);
        }

        return $this;
    }

    public function add(string ...$names): static
    {
        foreach ($names as $name) {
            $this->addChild(AutoField::create($name));
        }

        return $this;
    }

    public function hasChild(string $name): bool
    {
        return isset($this->children[$name]);
    }

    public function getChild(string $name): ?ElementInterface
    {
        return $this->children[$name] ?? null;
    }

    public function removeChild(string $name): static
    {
        unset($this->children[$name]);

        return $this;
    }

    public function replaceChild(string $name, ElementInterface $element): static
    {
        if (!isset($this->children[$name])) {
            return $this->addChild($element);
        }

        $children = [];

        foreach ($this->children as $key => $child) {
            if ($key === $name) {
                $children[$element->getName()] = $element;

                continue;
            }

            $children[$key] = $child;
        }

        $this->children = $children;

        return $this;
    }

    public function getChildren(): array
    {
        $children = array_filter($this->children, static fn (ElementInterface $child): bool => $child->isEnabled());

        // uasort is stable since PHP 8.0: insertion order is kept for equal priorities.
        uasort($children, static fn (ElementInterface $a, ElementInterface $b): int => $b->getPriority() <=> $a->getPriority());

        return $children;
    }

    public function getAllChildren(): array
    {
        return $this->children;
    }

    public function find(string $path): ?ElementInterface
    {
        $element = $this;

        foreach (explode('.', $path) as $name) {
            if (!$element instanceof CompositeElementInterface) {
                return null;
            }

            $element = $element->getChild($name);

            if (null === $element) {
                return null;
            }
        }

        return $element;
    }
}
