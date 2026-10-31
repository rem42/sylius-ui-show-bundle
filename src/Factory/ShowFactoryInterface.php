<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Factory;

use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Exception\ShowNotFoundException;

interface ShowFactoryInterface
{
    /**
     * Builds the show of a resource, using the given show name or the show supporting the resource class.
     *
     * @throws ShowNotFoundException
     */
    public function create(object $resource, ?string $name = null): ShowBuilder;

    public function supports(object $resource, ?string $name = null): bool;
}
