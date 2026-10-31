<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Event;

use Rem42\SyliusUiShow\Builder\ShowBuilder;

/**
 * Dispatched once a show definition has been built, before auto fields are resolved.
 * Listeners can add, remove, reorder or alter elements (e.g. from another bundle).
 */
final class ShowBuildEvent
{
    public function __construct(
        private readonly string $name,
        private readonly ShowBuilder $show,
        private readonly object $resource,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getShow(): ShowBuilder
    {
        return $this->show;
    }

    public function getResource(): object
    {
        return $this->resource;
    }
}
