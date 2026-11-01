<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional;

use App\Entity\Product;
use App\Kernel;
use Rem42\SyliusUiShow\Factory\ShowFactoryInterface;
use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\DateTimeField;
use Rem42\SyliusUiShow\Model\Field\EnumField;
use Rem42\SyliusUiShow\Model\Field\NumberField;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Smoke-tests the demo application (tests/Application) served by docker-compose.
 *
 * @internal
 */
final class DemoApplicationTest extends KernelTestCase
{
    public function testTheProductShowPageRenders(): void
    {
        $kernel = self::bootKernel();
        $response = $kernel->handle(Request::create('/'));
        $html = (string) $response->getContent();

        self::assertSame(200, $response->getStatusCode(), $html);
        self::assertStringContainsString('<h1 class="page-title">Super Gadget 42</h1>', $html);
        self::assertMatchesRegularExpression('/list-group-item-action active"[^>]*>Dashboard</', $html);
        self::assertStringContainsString('status status-success', $html);
        self::assertStringContainsString('style="width: 85%"', $html);
        self::assertStringContainsString('<a href="https://example.com" target="_blank" rel="noopener noreferrer">https://example.com</a>', $html);
        self::assertStringContainsString('Consumer Electronics', $html);
        self::assertStringContainsString('<td>Variant B (Red)</td>', $html);
        self::assertStringContainsString('Resource Name: Super Gadget 42', $html);
        self::assertStringContainsString('<span class="badge bg-success-lt">PUBLISHED</span>', $html);
        self::assertStringContainsString('Published at', $html);
        // fields of the "Texts, numbers & dates" item
        self::assertStringContainsString('A gadget that does everything.<br />', $html);
        self::assertStringContainsString('A gadget that does everything.…', $html);
        self::assertStringContainsString('<span class="badge bg-success-lt">Yes</span>', $html);
        self::assertStringContainsString('<span class="badge bg-success-lt">1,234 in stock</span>', $html);
        self::assertStringContainsString('<i class="ti ti-rocket" aria-hidden="true"></i>', $html);
        self::assertStringContainsString('<path d="M12 2 2 7l10 5 10-5-10-5Z"/>', $html);
        self::assertStringContainsString('Rendered by a Twig hook', $html);
        self::assertStringContainsString('<a href="/shadcn/" class="btn">Shadcn</a>', $html);
    }

    public function testTheProductShowPageRendersWithTheShadcnTheme(): void
    {
        $kernel = self::bootKernel(['theme' => 'shadcn']);
        $response = $kernel->handle(Request::create('/shadcn/'));
        $html = (string) $response->getContent();

        self::assertSame(200, $response->getStatusCode(), $html);
        self::assertStringContainsString('<h1 class="text-2xl font-semibold tracking-tight">Super Gadget 42</h1>', $html);
        self::assertStringContainsString('data-controller="admin--tabs"', $html);
        self::assertStringContainsString('<script type="importmap"', $html);
        self::assertStringContainsString('data-slot="badge"', $html);
        self::assertStringContainsString('1,234 in stock', $html);
        self::assertStringContainsString('data-slot="alert"', $html);
        self::assertStringContainsString('Resource Name: Super Gadget 42', $html);
        self::assertStringContainsString('<a href="/shadcn/" class="font-medium hover:underline">Variant B (Red)</a>', $html);
        self::assertStringContainsString('<a href="/"', $html);
    }

    public function testDoctrineMappingIsUsedToGuessFields(): void
    {
        self::bootKernel();
        $factory = self::getContainer()->get(ShowFactoryInterface::class);
        \assert($factory instanceof ShowFactoryInterface);

        $show = $factory->create(new Product());
        $identity = 'dashboard.main.top_section.left.identity.';

        self::assertInstanceOf(NumberField::class, $show->find($identity . 'price'));
        self::assertInstanceOf(DateTimeField::class, $show->find($identity . 'publishedAt'));
        self::assertSame('Y-m-d', $show->find($identity . 'publishedAt')?->getOption('format'));
        self::assertInstanceOf(EnumField::class, $show->find('more.details_table.properties.statusEnum'));
        self::assertInstanceOf(AssociationField::class, $show->find('more.datagrid.properties_grid.category'));
    }

    /**
     * @param array{environment?: string, debug?: bool, theme?: string} $options
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        return new Kernel($options['environment'] ?? 'test', $options['debug'] ?? true, $options['theme'] ?? Kernel::DEFAULT_THEME);
    }
}
