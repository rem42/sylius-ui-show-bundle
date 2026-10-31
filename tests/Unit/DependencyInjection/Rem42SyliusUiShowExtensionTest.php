<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Rem42\SyliusUiShow\Symfony\DependencyInjection\Configuration;
use Rem42\SyliusUiShow\Symfony\DependencyInjection\Rem42SyliusUiShowExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @internal
 */
final class Rem42SyliusUiShowExtensionTest extends AbstractExtensionTestCase
{
    public function testItRegistersServicesAndDefaultTemplates(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('rem42_sylius_ui_show.registry');
        $this->assertContainerBuilderHasService('rem42_sylius_ui_show.factory');
        $this->assertContainerBuilderHasService('rem42_sylius_ui_show.renderer');
        $this->assertContainerBuilderHasService('rem42_sylius_ui_show.field_guesser.doctrine');
        $this->assertContainerBuilderHasParameter('rem42_sylius_ui_show.templates', Configuration::getThemeTemplates('@Rem42SyliusUiShow/bootstrap'));
        $this->assertContainerBuilderHasParameter('rem42_sylius_ui_show.label_prefix', 'sylius.ui.');
    }

    public function testTemplatesCanBeOverridden(): void
    {
        $this->load(['templates' => ['card' => 'app/card.html.twig', 'money' => 'app/money.html.twig'], 'label_prefix' => 'app.ui.']);

        /** @var array<string, string> $templates */
        $templates = $this->container->getParameter('rem42_sylius_ui_show.templates');

        self::assertSame('app/card.html.twig', $templates['card']);
        self::assertSame('app/money.html.twig', $templates['money']);
        self::assertSame('@Rem42SyliusUiShow/bootstrap/field/string.html.twig', $templates['string']);
        $this->assertContainerBuilderHasParameter('rem42_sylius_ui_show.label_prefix', 'app.ui.');
    }

    public function testEveryThemeTemplateExists(): void
    {
        foreach (Configuration::BUILTIN_THEMES as $directory) {
            foreach (Configuration::getThemeTemplates($directory) as $template) {
                self::assertFileExists(str_replace('@Rem42SyliusUiShow', __DIR__ . '/../../../templates', $template));
            }
        }
    }

    public function testTheShadcnThemeIsTheDefaultWithShadcnAdminUi(): void
    {
        $this->container->setParameter('kernel.bundles', ['Rem42ShadcnAdminUiBundle' => \stdClass::class]);
        $this->load();

        $this->assertContainerBuilderHasParameter('rem42_sylius_ui_show.templates', Configuration::getThemeTemplates('@Rem42SyliusUiShow/shadcn'));
    }

    public function testTheThemeCanBeChosen(): void
    {
        $this->container->setParameter('kernel.bundles', ['Rem42ShadcnAdminUiBundle' => \stdClass::class]);
        $this->load(['theme' => 'bootstrap']);

        $this->assertContainerBuilderHasParameter('rem42_sylius_ui_show.templates', Configuration::getThemeTemplates('@Rem42SyliusUiShow/bootstrap'));
    }

    public function testAThemeCanBeRegistered(): void
    {
        $this->load(['themes' => ['acme' => '@AcmeAdmin/show/'], 'theme' => 'acme', 'templates' => ['card' => 'app/card.html.twig']]);

        /** @var array<string, string> $templates */
        $templates = $this->container->getParameter('rem42_sylius_ui_show.templates');

        self::assertSame('@AcmeAdmin/show/field/string.html.twig', $templates['string']);
        self::assertSame('@AcmeAdmin/show/integration/twig_hooks.html.twig', $templates['twig_hooks']);
        self::assertSame('app/card.html.twig', $templates['card']);
    }

    public function testAnUnknownThemeIsRejected(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Unknown theme "acme" in "rem42_sylius_ui_show.theme", expected one of "bootstrap", "shadcn".');

        $this->load(['theme' => 'acme']);
    }

    public function testItRegistersTheTwigHookWhenTwigHooksIsInstalled(): void
    {
        $this->container->setParameter('kernel.bundles', ['SyliusTwigHooksBundle' => \stdClass::class]);
        $this->container->prependExtensionConfig('rem42_sylius_ui_show', ['twig_hooks' => ['priority' => 10]]);

        (new Rem42SyliusUiShowExtension())->prepend($this->container);

        self::assertSame([[
            'hooks' => [
                'sylius_admin.common.show.content' => [
                    'rem42_sylius_ui_show' => [
                        'template' => '@Rem42SyliusUiShow/integration/twig_hooks/show.html.twig',
                        'priority' => 10,
                    ],
                ],
            ],
        ]], $this->container->getExtensionConfig('sylius_twig_hooks'));
    }

    public function testTheTwigHookCanBeDisabled(): void
    {
        $this->container->setParameter('kernel.bundles', ['SyliusTwigHooksBundle' => \stdClass::class]);
        $this->container->prependExtensionConfig('rem42_sylius_ui_show', ['twig_hooks' => false]);

        (new Rem42SyliusUiShowExtension())->prepend($this->container);

        self::assertSame([], $this->container->getExtensionConfig('sylius_twig_hooks'));
    }

    public function testNothingIsPrependedWithoutTwigHooks(): void
    {
        $this->container->setParameter('kernel.bundles', []);

        (new Rem42SyliusUiShowExtension())->prepend($this->container);

        self::assertSame([], $this->container->getExtensionConfig('sylius_twig_hooks'));
    }

    protected function getContainerExtensions(): array
    {
        return [new Rem42SyliusUiShowExtension()];
    }
}
