<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Rem42\SyliusUiShow\Accessor\ValueAccessor;
use Rem42\SyliusUiShow\Accessor\ValueAccessorInterface;
use Rem42\SyliusUiShow\Factory\ShowFactory;
use Rem42\SyliusUiShow\Factory\ShowFactoryInterface;
use Rem42\SyliusUiShow\Registry\ShowRegistry;
use Rem42\SyliusUiShow\Registry\ShowRegistryInterface;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('rem42_sylius_ui_show.registry', ShowRegistry::class)
        ->args([
            '$shows' => service_locator([]),
            '$resourceClasses' => [],
        ])
    ;
    $services->alias(ShowRegistryInterface::class, 'rem42_sylius_ui_show.registry');

    $services->set('rem42_sylius_ui_show.value_accessor', ValueAccessor::class)
        ->args([service('property_accessor')->nullOnInvalid()])
    ;
    $services->alias(ValueAccessorInterface::class, 'rem42_sylius_ui_show.value_accessor');

    $services->set('rem42_sylius_ui_show.factory', ShowFactory::class)
        ->args([
            service('rem42_sylius_ui_show.registry'),
            service('rem42_sylius_ui_show.auto_field_resolver'),
            service('event_dispatcher')->nullOnInvalid(),
        ])
    ;
    $services->alias(ShowFactoryInterface::class, 'rem42_sylius_ui_show.factory');
};
