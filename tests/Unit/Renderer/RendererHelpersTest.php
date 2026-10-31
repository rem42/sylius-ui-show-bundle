<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Unit\Renderer;

use PHPUnit\Framework\TestCase;
use Rem42\SyliusUiShow\Accessor\ValueAccessor;
use Rem42\SyliusUiShow\Exception\TemplateNotFoundException;
use Rem42\SyliusUiShow\Model\Field\StringField;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Rem42\SyliusUiShow\Model\Layout\Template;
use Rem42\SyliusUiShow\Renderer\LabelGenerator;
use Rem42\SyliusUiShow\Renderer\TemplateResolver;

/**
 * @internal
 */
final class RendererHelpersTest extends TestCase
{
    public function testLabelGenerator(): void
    {
        $generator = new LabelGenerator('app.ui.');

        self::assertSame('app.ui.created_at', $generator->generate(StringField::create('createdAt')));
        self::assertSame('app.ui.customer_email', $generator->generate(StringField::create('customer.email')));
        self::assertSame('app.ui.sku', $generator->generate(StringField::create('SKU')));
        self::assertSame('Custom', $generator->generate(StringField::create('name')->setLabel('Custom')));
        self::assertSame('', $generator->generate(StringField::create('name')->setLabel(false)));
    }

    public function testTemplateResolver(): void
    {
        $resolver = new TemplateResolver(['card' => 'card.html.twig', 'field_row' => 'row.html.twig']);

        self::assertSame('card.html.twig', $resolver->resolve(Card::create('card')));
        self::assertSame('custom.html.twig', $resolver->resolve(Card::create('card')->setTemplate('custom.html.twig')));
        self::assertSame('row.html.twig', $resolver->getFieldRowTemplate());
        self::assertSame('card.html.twig', $resolver->getTemplate('card'));

        $this->expectException(TemplateNotFoundException::class);
        $resolver->resolve(Template::create('no_template'));
    }

    public function testTemplateResolverThrowsForAnUnknownType(): void
    {
        $this->expectException(TemplateNotFoundException::class);
        $this->expectExceptionMessage('No template configured for type "twig_hooks".');

        (new TemplateResolver([]))->getTemplate('twig_hooks');
    }

    public function testValueAccessor(): void
    {
        $accessor = new ValueAccessor();
        $resource = new class {
            public ?object $customer = null;

            public string $name = 'John';
        };

        self::assertSame($resource, $accessor->getValue($resource, '.'));
        self::assertSame('John', $accessor->getValue($resource, 'name'));
        self::assertNull($accessor->getValue($resource, 'customer.email'));
        self::assertSame('bar', $accessor->getValue(['foo' => 'bar'], '[foo]'));
        self::assertNull($accessor->getValue('scalar', 'name'));
    }
}
