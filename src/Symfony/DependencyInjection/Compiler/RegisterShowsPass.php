<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Symfony\DependencyInjection\Compiler;

use Rem42\SyliusUiShow\Exception\InvalidShowException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Collects the services tagged "rem42_sylius_ui_show.show" (automatically added by #[AsShow]) into the show registry.
 */
final class RegisterShowsPass implements CompilerPassInterface
{
    public const SHOW_TAG = 'rem42_sylius_ui_show.show';

    public const REGISTRY_ID = 'rem42_sylius_ui_show.registry';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::REGISTRY_ID)) {
            return;
        }

        $references = [];
        $shows = [];

        foreach ($container->findTaggedServiceIds(self::SHOW_TAG) as $id => $tags) {
            foreach ($tags as $tag) {
                if (!\is_array($tag)) {
                    continue;
                }

                $name = $tag['name'] ?? null;
                $name = \is_string($name) && '' !== $name ? $name : $id;
                $resourceClass = $tag['resource_class'] ?? null;

                if (!\is_string($resourceClass) || '' === $resourceClass) {
                    throw new InvalidShowException(sprintf('The "%s" tag of service "%s" requires a "resource_class" attribute.', self::SHOW_TAG, $id));
                }

                if (isset($references[$name])) {
                    throw new InvalidShowException(sprintf('Show "%s" is registered twice (services "%s" and "%s").', $name, (string) $references[$name], $id));
                }

                $references[$name] = new Reference($id);
                $shows[] = ['name' => $name, 'resource_class' => $resourceClass, 'priority' => \is_int($tag['priority'] ?? null) ? $tag['priority'] : 0];
            }
        }

        // Highest priority first; stable for equal priorities.
        usort($shows, static fn (array $a, array $b): int => $b['priority'] <=> $a['priority']);

        $container->getDefinition(self::REGISTRY_ID)
            ->setArgument('$shows', ServiceLocatorTagPass::register($container, $references))
            ->setArgument('$resourceClasses', array_column($shows, 'resource_class', 'name'))
        ;
    }
}
