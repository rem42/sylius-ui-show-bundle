<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Symfony;

use Rem42\SyliusUiShow\Symfony\DependencyInjection\Compiler\RegisterShowsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class Rem42SyliusUiShowBundle extends Bundle
{
    /**
     * The package root (containing "config/" and "templates/") is the bundle path.
     */
    public function getPath(): string
    {
        return \dirname(__DIR__, 2);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterShowsPass());
    }
}
