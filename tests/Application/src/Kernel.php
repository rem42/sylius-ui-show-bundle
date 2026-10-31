<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Demo application showcasing every element of the bundle, once per theme (see docker-compose.yml):
 * - bootstrap: http://localhost:8081/admin/show/product/1
 * - shadcn: http://localhost:8081/shadcn/admin/show/product/1
 *
 * Each theme gets its own container: bundles flagged with its name in config/bundles.php, configuration of
 * config/packages/{theme}/ and templates of templates/themes/{theme}/, which take precedence over templates/.
 */
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const THEMES = ['bootstrap', 'shadcn'];

    public const DEFAULT_THEME = 'bootstrap';

    public function __construct(
        string $environment,
        bool $debug,
        private readonly string $theme = self::DEFAULT_THEME,
    ) {
        if (!\in_array($theme, self::THEMES, true)) {
            throw new \InvalidArgumentException(sprintf('Unknown theme "%s", expected one of "%s".', $theme, implode('", "', self::THEMES)));
        }

        parent::__construct($environment, $debug);
    }

    /**
     * Theme serving a path: "/shadcn/..." is rendered by the shadcn theme, anything else by the default one.
     */
    public static function getThemeForPath(string $path): string
    {
        $prefix = explode('/', ltrim($path, '/'), 2)[0];

        return \in_array($prefix, self::THEMES, true) ? $prefix : self::DEFAULT_THEME;
    }

    /**
     * Prefix of the URLs served by a theme: none for the default one, "/{theme}" for the others.
     */
    public static function getPathPrefix(string $theme): string
    {
        return self::DEFAULT_THEME === $theme ? '' : '/' . $theme;
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return sprintf('%s/var/cache/%s/%s', $this->getProjectDir(), $this->theme, $this->environment);
    }

    public function registerBundles(): iterable
    {
        /** @var array<class-string<\Symfony\Component\HttpKernel\Bundle\BundleInterface>, array<string, bool>> $bundles */
        $bundles = require $this->getProjectDir() . '/config/bundles.php';

        foreach ($bundles as $class => $themes) {
            if ($themes[$this->theme] ?? $themes['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function getContainerClass(): string
    {
        return sprintf('App_%s%s%sContainer', ucfirst($this->theme), ucfirst($this->environment), $this->debug ? 'Debug' : '');
    }

    private function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/packages/*.yaml');
        $container->import('../config/{packages}/' . $this->theme . '/*.yaml');
        $container->import('../config/services.yaml');

        $container->parameters()->set('app.theme', $this->theme);

        $container->extension('rem42_sylius_ui_show', ['theme' => $this->theme]);
        $container->extension('twig', ['paths' => ['%kernel.project_dir%/templates/themes/' . $this->theme => null]]);
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/routes.yaml')->prefix(self::getPathPrefix($this->theme));
    }
}
