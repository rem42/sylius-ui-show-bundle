<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Registry;

use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Exception\ShowNotFoundException;

interface ShowRegistryInterface
{
    public function has(string $name): bool;

    /**
     * @return callable(ShowBuilder, object): void
     *
     * @throws ShowNotFoundException
     */
    public function get(string $name): callable;

    /**
     * @return list<string>
     */
    public function getNames(): array;

    /**
     * Finds the name of the show supporting the given resource (or class), walking up its parents and interfaces.
     *
     * @param object|class-string $resource
     */
    public function findNameFor(object|string $resource): ?string;
}
