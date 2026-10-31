<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Symfony\DependencyInjection;

use Doctrine\Persistence\ManagerRegistry;
use Rem42\SyliusUiShow\Attribute\AsShow;
use Rem42\SyliusUiShow\Guesser\FieldGuesserInterface;
use Rem42\SyliusUiShow\Symfony\DependencyInjection\Compiler\RegisterShowsPass;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class Rem42SyliusUiShowExtension extends Extension implements PrependExtensionInterface
{
    public const FIELD_GUESSER_TAG = 'rem42_sylius_ui_show.field_guesser';

    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var array{label_prefix: string, theme: ?string, themes: array<string, string>, templates: array<string, string>} $config */
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../../config'));
        $loader->load('services.php');

        if (interface_exists(ManagerRegistry::class)) {
            $loader->load('integration/doctrine.php');
        }

        $container->setParameter('rem42_sylius_ui_show.templates', array_merge(Configuration::getThemeTemplates($this->getThemeDirectory($config, $container)), $config['templates']));
        $container->setParameter('rem42_sylius_ui_show.label_prefix', $config['label_prefix']);

        $container->registerForAutoconfiguration(FieldGuesserInterface::class)->addTag(self::FIELD_GUESSER_TAG);

        $container->registerAttributeForAutoconfiguration(
            AsShow::class,
            static function (ChildDefinition $definition, AsShow $attribute): void {
                $tag = [
                    'resource_class' => $attribute->resourceClass,
                    'priority' => $attribute->priority,
                ];

                // A null tag attribute breaks the XML container dump in debug mode.
                if (null !== $attribute->name) {
                    $tag['name'] = $attribute->name;
                }

                $definition->addTag(RegisterShowsPass::SHOW_TAG, $tag);
            },
        );
    }

    public function prepend(ContainerBuilder $container): void
    {
        /** @var array<string, class-string> $bundles */
        $bundles = $container->hasParameter('kernel.bundles') ? $container->getParameter('kernel.bundles') : [];

        if (!isset($bundles['SyliusTwigHooksBundle'])) {
            return;
        }

        /** @var array{twig_hooks: array{enabled: bool, hook: string, hookable: string, priority: int}} $config */
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));
        $twigHooks = $config['twig_hooks'];

        if (!$twigHooks['enabled']) {
            return;
        }

        $container->prependExtensionConfig('sylius_twig_hooks', [
            'hooks' => [
                $twigHooks['hook'] => [
                    $twigHooks['hookable'] => [
                        'template' => '@Rem42SyliusUiShow/integration/twig_hooks/show.html.twig',
                        'priority' => $twigHooks['priority'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Resolved here rather than in the configuration tree: the default theme depends on the enabled bundles, and
     * themes may be registered by bundles prepending their configuration after this bundle processed its own.
     *
     * @param array{theme: ?string, themes: array<string, string>} $config
     */
    private function getThemeDirectory(array $config, ContainerBuilder $container): string
    {
        $themes = array_merge(Configuration::BUILTIN_THEMES, $config['themes']);
        $theme = $config['theme'] ?? $this->getDefaultTheme($container);

        return $themes[$theme] ?? throw new InvalidConfigurationException(sprintf('Unknown theme "%s" in "rem42_sylius_ui_show.theme", expected one of "%s". Register it in "rem42_sylius_ui_show.themes".', $theme, implode('", "', array_keys($themes))));
    }

    private function getDefaultTheme(ContainerBuilder $container): string
    {
        /** @var array<string, class-string> $bundles */
        $bundles = $container->hasParameter('kernel.bundles') ? $container->getParameter('kernel.bundles') : [];

        foreach (Configuration::DEFAULT_THEMES_BY_BUNDLE as $bundle => $theme) {
            if (isset($bundles[$bundle])) {
                return $theme;
            }
        }

        return Configuration::DEFAULT_THEME;
    }
}
