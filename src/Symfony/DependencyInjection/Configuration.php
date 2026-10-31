<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /** Templates every theme must provide, relative to its directory. */
    public const TEMPLATES = [
        // Rows
        'field_row' => 'field/_row.html.twig',

        // Layouts
        'show' => 'show.html.twig',
        'card' => 'layout/card.html.twig',
        'column' => 'layout/column.html.twig',
        'datagrid' => 'layout/datagrid.html.twig',
        'details_table' => 'layout/details_table.html.twig',
        'hook' => 'layout/hook.html.twig',
        'section' => 'layout/section.html.twig',
        'side_nav' => 'layout/side_nav.html.twig',
        'side_nav_item' => 'layout/children.html.twig',
        'side_nav_divider' => 'layout/side_nav_divider.html.twig',
        'tab' => 'layout/children.html.twig',
        'tabs' => 'layout/tabs.html.twig',

        // Integrations
        'twig_hooks' => 'integration/twig_hooks.html.twig',

        // Fields
        'association' => 'field/association.html.twig',
        'auto' => 'field/string.html.twig',
        'badge' => 'field/badge.html.twig',
        'boolean' => 'field/boolean.html.twig',
        'collection' => 'field/collection.html.twig',
        'datetime' => 'field/datetime.html.twig',
        'enum' => 'field/enum.html.twig',
        'file' => 'field/file.html.twig',
        'icon' => 'field/icon.html.twig',
        'image' => 'field/image.html.twig',
        'number' => 'field/number.html.twig',
        'progress' => 'field/progress.html.twig',
        'sensitive' => 'field/sensitive.html.twig',
        'status' => 'field/status.html.twig',
        'string' => 'field/string.html.twig',
        'url' => 'field/url.html.twig',
    ];

    /**
     * Themes shipped with the bundle: name => template directory.
     *
     * - bootstrap: Tabler markup of sylius/bootstrap-admin-ui;
     * - shadcn: Tailwind CSS markup of rem42/shadcn-admin-ui (palette, badge/dot components, admin--tabs controller).
     */
    public const BUILTIN_THEMES = [
        'bootstrap' => '@Rem42SyliusUiShow/bootstrap',
        'shadcn' => '@Rem42SyliusUiShow/shadcn',
    ];

    /** Default theme when "rem42_sylius_ui_show.theme" is not configured: the one matching the admin UI bundle installed. */
    public const DEFAULT_THEME = 'bootstrap';

    public const DEFAULT_THEMES_BY_BUNDLE = ['Rem42ShadcnAdminUiBundle' => 'shadcn'];

    public const DEFAULT_TWIG_HOOK = 'sylius_admin.common.show.content';

    /**
     * @param string $directory template directory of the theme, e.g. "@Rem42SyliusUiShow/bootstrap"
     *
     * @return array<string, string> element type => template
     */
    public static function getThemeTemplates(string $directory): array
    {
        $directory = rtrim($directory, '/');

        return array_map(static fn (string $template): string => $directory . '/' . $template, self::TEMPLATES);
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('rem42_sylius_ui_show');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode('label_prefix')
            ->info('Prefix of the translation keys generated for elements without an explicit label.')
            ->defaultValue('sylius.ui.')
            ->end()
            ->scalarNode('theme')
            ->info('Theme providing the default templates: "bootstrap" (sylius/bootstrap-admin-ui), "shadcn" (rem42/shadcn-admin-ui) or one of "themes". Defaults to "shadcn" when Rem42ShadcnAdminUiBundle is enabled, "bootstrap" otherwise.')
            ->defaultNull()
            ->end()
            ->arrayNode('themes')
            ->info('Themes provided by other bundles or the application: name => template directory.')
            ->example(['acme' => '@AcmeAdmin/show'])
            ->useAttributeAsKey('name')
            ->scalarPrototype()->cannotBeEmpty()->end()
            ->end()
            ->end()
        ;

        $this->addTemplatesSection($rootNode);
        $this->addTwigHooksSection($rootNode);

        return $treeBuilder;
    }

    private function addTemplatesSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
            ->arrayNode('templates')
            ->info('Templates used to render each element type, merged with the ones of the theme.')
            ->example(['card' => 'show/layout/card.html.twig', 'money' => 'show/field/money.html.twig'])
            ->useAttributeAsKey('type')
            ->scalarPrototype()->cannotBeEmpty()->end()
            ->end()
            ->end()
        ;
    }

    private function addTwigHooksSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
            ->arrayNode('twig_hooks')
            ->info('Sylius Twig Hooks integration: renders the show of the resource on Sylius Admin UI show pages.')
            ->canBeDisabled()
            ->children()
            ->scalarNode('hook')->defaultValue(self::DEFAULT_TWIG_HOOK)->cannotBeEmpty()->end()
            ->scalarNode('hookable')->defaultValue('rem42_sylius_ui_show')->cannotBeEmpty()->end()
            ->integerNode('priority')->defaultValue(-50)->end()
            ->end()
            ->end()
            ->end()
        ;
    }
}
