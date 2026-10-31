<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Rem42\SyliusUiShow\Renderer\LabelGenerator;
use Rem42\SyliusUiShow\Renderer\ShowRendererInterface;
use Rem42\SyliusUiShow\Renderer\TemplateResolver;
use Rem42\SyliusUiShow\Renderer\TemplateResolverInterface;
use Rem42\SyliusUiShow\Renderer\TwigShowRenderer;
use Rem42\SyliusUiShow\Twig\Extension\ShowExtension;
use Rem42\SyliusUiShow\Twig\Runtime\ShowRuntime;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('rem42_sylius_ui_show.template_resolver', TemplateResolver::class)
        ->args([param('rem42_sylius_ui_show.templates')])
    ;
    $services->alias(TemplateResolverInterface::class, 'rem42_sylius_ui_show.template_resolver');

    $services->set('rem42_sylius_ui_show.label_generator', LabelGenerator::class)
        ->args([param('rem42_sylius_ui_show.label_prefix')])
    ;

    $services->set('rem42_sylius_ui_show.renderer', TwigShowRenderer::class)
        ->args([
            service('twig'),
            service('rem42_sylius_ui_show.template_resolver'),
            service('rem42_sylius_ui_show.value_accessor'),
        ])
    ;
    $services->alias(ShowRendererInterface::class, 'rem42_sylius_ui_show.renderer');

    $services->set('rem42_sylius_ui_show.twig.extension', ShowExtension::class)
        ->tag('twig.extension')
    ;

    $services->set('rem42_sylius_ui_show.twig.runtime', ShowRuntime::class)
        ->args([
            service('rem42_sylius_ui_show.factory'),
            service('rem42_sylius_ui_show.renderer'),
            service('rem42_sylius_ui_show.value_accessor'),
            service('rem42_sylius_ui_show.label_generator'),
            service('rem42_sylius_ui_show.template_resolver'),
        ])
        ->tag('twig.runtime')
    ;
};
