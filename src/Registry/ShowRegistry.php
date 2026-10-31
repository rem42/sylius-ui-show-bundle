<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Registry;

use Psr\Container\ContainerInterface;
use Rem42\SyliusUiShow\Exception\InvalidShowException;
use Rem42\SyliusUiShow\Exception\ShowNotFoundException;

final class ShowRegistry implements ShowRegistryInterface
{
    /** @var array<class-string, string|null> */
    private array $resolved = [];

    /**
     * @param ContainerInterface    $shows           show definitions indexed by name
     * @param array<string, string> $resourceClasses show name => supported resource class, sorted by priority (highest first)
     */
    public function __construct(
        private readonly ContainerInterface $shows,
        private readonly array $resourceClasses,
    ) {}

    public function has(string $name): bool
    {
        return $this->shows->has($name);
    }

    public function get(string $name): callable
    {
        if (!$this->shows->has($name)) {
            throw ShowNotFoundException::forName($name);
        }

        $show = $this->shows->get($name);

        if (!\is_callable($show)) {
            throw new InvalidShowException(sprintf('Show "%s" (%s) must be invokable: add an "__invoke(ShowBuilder $show, object $resource): void" method.', $name, get_debug_type($show)));
        }

        return $show;
    }

    public function getNames(): array
    {
        return array_keys($this->resourceClasses);
    }

    public function findNameFor(object|string $resource): ?string
    {
        $class = \is_object($resource) ? $resource::class : $resource;

        if (\array_key_exists($class, $this->resolved)) {
            return $this->resolved[$class];
        }

        $candidates = [$class, ...array_values(class_parents($class) ?: []), ...array_values(class_implements($class) ?: [])];

        // Exact class first, then the closest parent, then interfaces.
        foreach ($candidates as $candidate) {
            foreach ($this->resourceClasses as $name => $resourceClass) {
                if ($resourceClass === $candidate) {
                    return $this->resolved[$class] = $name;
                }
            }
        }

        return $this->resolved[$class] = null;
    }
}
