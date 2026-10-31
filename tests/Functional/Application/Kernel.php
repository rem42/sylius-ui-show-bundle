<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional\Application;

use Rem42\SyliusUiShow\Symfony\Rem42SyliusUiShowBundle;
use Sylius\TwigHooks\SyliusTwigHooksBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\UX\TwigComponent\TwigComponentBundle;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new TwigComponentBundle();
        yield new SyliusTwigHooksBundle();
        yield new Rem42SyliusUiShowBundle();
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test' => true,
            'secret' => 'test',
            'http_method_override' => false,
            'form' => false,
            'property_info' => ['enabled' => true],
            'translator' => ['default_path' => '%kernel.project_dir%/translations', 'fallbacks' => ['en']],
            'router' => ['utf8' => true],
        ]);

        $container->extension('twig', [
            'default_path' => '%kernel.project_dir%/templates',
            'strict_variables' => true,
        ]);

        $container->extension('twig_component', [
            'anonymous_template_directory' => 'components/',
            'defaults' => [],
        ]);

        $container->extension('sylius_twig_hooks', [
            'hooks' => [
                'app.book.show.extra' => [
                    'extra' => ['template' => 'hookable/extra.html.twig'],
                ],
                'sylius_admin.book.show.content' => [
                    'rem42_sylius_ui_show' => [
                        'template' => '@Rem42SyliusUiShow/integration/twig_hooks/show.html.twig',
                        'configuration' => ['show' => 'app_book_compact'],
                    ],
                ],
            ],
        ]);

        $container->services()
            ->defaults()->autowire()->autoconfigure()
            ->load(__NAMESPACE__ . '\Show\\', __DIR__ . '/Show/')
        ;
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->add('app_author_show', '/authors/{id}');
    }
}
