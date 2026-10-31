<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Rem42\SyliusUiShow\Guesser\AutoFieldResolver;
use Rem42\SyliusUiShow\Guesser\AutoFieldResolverInterface;
use Rem42\SyliusUiShow\Guesser\ChainFieldGuesser;
use Rem42\SyliusUiShow\Guesser\FieldGuesserInterface;
use Rem42\SyliusUiShow\Guesser\NameFieldGuesser;
use Rem42\SyliusUiShow\Guesser\PropertyInfoFieldGuesser;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('rem42_sylius_ui_show.field_guesser', ChainFieldGuesser::class)
        ->args([tagged_iterator('rem42_sylius_ui_show.field_guesser')])
    ;
    $services->alias(FieldGuesserInterface::class, 'rem42_sylius_ui_show.field_guesser');

    $services->set('rem42_sylius_ui_show.field_guesser.property_info', PropertyInfoFieldGuesser::class)
        ->args([service('property_info')->nullOnInvalid()])
        ->tag('rem42_sylius_ui_show.field_guesser', ['priority' => 100])
    ;

    $services->set('rem42_sylius_ui_show.field_guesser.name', NameFieldGuesser::class)
        ->tag('rem42_sylius_ui_show.field_guesser', ['priority' => 0])
    ;

    $services->set('rem42_sylius_ui_show.auto_field_resolver', AutoFieldResolver::class)
        ->args([service('rem42_sylius_ui_show.field_guesser')])
    ;
    $services->alias(AutoFieldResolverInterface::class, 'rem42_sylius_ui_show.auto_field_resolver');
};
