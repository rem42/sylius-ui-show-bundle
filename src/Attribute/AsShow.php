<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Attribute;

/**
 * Registers an invokable class as a show definition:
 *
 *     #[AsShow(resourceClass: Product::class, name: 'app_product')]
 *     final class ProductShow
 *     {
 *         public function __invoke(ShowBuilder $show, Product $product): void {}
 *     }
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsShow
{
    /**
     * @param class-string $resourceClass class (or interface) of the resources this show can display
     * @param string|null  $name          unique name of the show, defaults to the service id
     * @param int          $priority      the show with the highest priority is used when several shows support the same resource
     */
    public function __construct(
        public readonly string $resourceClass,
        public readonly ?string $name = null,
        public readonly int $priority = 0,
    ) {}
}
