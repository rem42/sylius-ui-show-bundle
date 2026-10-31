<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Rem42\SyliusUiShow\Guesser\DoctrineFieldGuesser;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('rem42_sylius_ui_show.field_guesser.doctrine', DoctrineFieldGuesser::class)
        ->args([service('doctrine')->nullOnInvalid()])
        ->tag('rem42_sylius_ui_show.field_guesser', ['priority' => 200])
    ;
};
