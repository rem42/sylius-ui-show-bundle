<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Factory;

use Psr\EventDispatcher\EventDispatcherInterface;
use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Event\ShowBuildEvent;
use Rem42\SyliusUiShow\Exception\ShowNotFoundException;
use Rem42\SyliusUiShow\Guesser\AutoFieldResolverInterface;
use Rem42\SyliusUiShow\Registry\ShowRegistryInterface;

final class ShowFactory implements ShowFactoryInterface
{
    public function __construct(
        private readonly ShowRegistryInterface $registry,
        private readonly AutoFieldResolverInterface $autoFieldResolver,
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
    ) {}

    public function create(object $resource, ?string $name = null): ShowBuilder
    {
        $name ??= $this->registry->findNameFor($resource) ?? throw ShowNotFoundException::forResource($resource);

        $show = ShowBuilder::create($name);
        ($this->registry->get($name))($show, $resource);

        $this->eventDispatcher?->dispatch(new ShowBuildEvent($name, $show, $resource));

        $this->autoFieldResolver->resolve($show, $resource::class);

        return $show;
    }

    public function supports(object $resource, ?string $name = null): bool
    {
        return null !== $name ? $this->registry->has($name) : null !== $this->registry->findNameFor($resource);
    }
}
