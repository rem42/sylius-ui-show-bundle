<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Unit\Model;

use PHPUnit\Framework\TestCase;
use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Exception\InvalidShowException;
use Rem42\SyliusUiShow\Model\Color;
use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\AutoField;
use Rem42\SyliusUiShow\Model\Field\BadgeField;
use Rem42\SyliusUiShow\Model\Field\EnumField;
use Rem42\SyliusUiShow\Model\Field\ProgressField;
use Rem42\SyliusUiShow\Model\Field\SensitiveField;
use Rem42\SyliusUiShow\Model\Field\StatusField;
use Rem42\SyliusUiShow\Model\Field\StringField;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Rem42\SyliusUiShow\Model\Layout\Column;
use Rem42\SyliusUiShow\Model\Layout\Section;

/**
 * @internal
 */
final class ElementTest extends TestCase
{
    public function testFieldPathDefaultsToItsName(): void
    {
        $field = StringField::create('title');

        self::assertSame('title', $field->getPath());
        self::assertSame('author.name', $field->setPath('author.name')->getPath());
        self::assertSame('author.name', StringField::create('author', ['path' => 'author.name'])->getPath());
    }

    public function testClassesAreMergedWithoutDuplicates(): void
    {
        $card = Card::create('card')->addClass('foo bar', 'foo')->addClass('baz');

        self::assertSame(['class' => 'foo bar baz'], $card->getAttributes());
        self::assertSame(['class' => 'foo baz'], $card->removeClass('bar')->getAttributes());
    }

    public function testChildrenAreSortedByPriorityAndDisabledOnesAreHidden(): void
    {
        $card = Card::create('card')->withChildren(
            StringField::create('a'),
            StringField::create('b')->setPriority(10),
            StringField::create('c')->setEnabled(false),
            StringField::create('d'),
        );

        self::assertSame(['b', 'a', 'd'], array_keys($card->getChildren()));
        self::assertSame(['a', 'b', 'c', 'd'], array_keys($card->getAllChildren()));
    }

    public function testAddCreatesAutoFields(): void
    {
        $card = Card::create('card')->add('name', 'createdAt');

        self::assertInstanceOf(AutoField::class, $card->getChild('name'));
        self::assertInstanceOf(AutoField::class, $card->getChild('createdAt'));
    }

    public function testReplaceChildKeepsThePosition(): void
    {
        $card = Card::create('card')->add('a', 'b', 'c')->replaceChild('b', StringField::create('x'));

        self::assertSame(['a', 'x', 'c'], array_keys($card->getAllChildren()));
    }

    public function testFindWalksTheTree(): void
    {
        $show = ShowBuilder::create('show')->withChildren(
            Section::create('main')->withChildren(
                Column::create('left')->withChildren(Card::create('card')->add('name')),
            ),
        );

        self::assertInstanceOf(AutoField::class, $show->find('main.left.card.name'));
        self::assertNull($show->find('main.left.unknown'));
        self::assertNull($show->find('main.left.card.name.deeper'));
    }

    public function testColumnAcceptsAWidthAsName(): void
    {
        $column = Column::create(6);

        self::assertSame(6, $column->getWidth());
        self::assertStringStartsWith('column_', $column->getName());
        self::assertSame('md', $column->getBreakpoint());
        self::assertSame(12, Column::create('full')->getWidth());
    }

    public function testFieldOptionsHaveTypedAccessors(): void
    {
        $badge = BadgeField::create('status', ['outline' => true, 'color_mapping' => ['active' => 'success']]);

        self::assertTrue($badge->isOutline());
        self::assertFalse($badge->isPill());
        self::assertSame(['active' => 'success'], $badge->getColorMapping());
        self::assertSame('secondary', $badge->getDefaultColor());
    }

    public function testColorsAcceptTheEnumOrItsValue(): void
    {
        $badge = BadgeField::create('status')
            ->setColorMapping(['active' => Color::Success, 'archived' => 'danger'])
            ->setDefaultColor(Color::Info)
        ;

        self::assertSame(['active' => 'success', 'archived' => 'danger'], $badge->getColorMapping());
        self::assertSame('info', $badge->getDefaultColor());
        self::assertSame('primary', AssociationField::create('customer')->getBadgeColor());
        self::assertNull(ProgressField::create('progress')->getColor());
        self::assertSame('warning', ProgressField::create('progress')->setColor(Color::Warning)->getColor());
        self::assertSame(['new' => 'info'], EnumField::create('state')->setBadge(['new' => Color::Info])->getBadge());
    }

    public function testThemeSpecificColorsAreRejected(): void
    {
        $this->expectException(InvalidShowException::class);
        $this->expectExceptionMessage('Invalid color "green", expected one of "primary", "secondary", "success", "info", "warning", "danger".');

        StatusField::create('status')->setColorMapping(['active' => 'green'])->getColorMapping();
    }

    public function testCardCanBeFlush(): void
    {
        self::assertFalse(Card::create('general')->isFlush());
        self::assertTrue(Card::create('general')->setFlush()->isFlush());
        self::assertFalse(Card::create('general')->setFlush()->setFlush(false)->isFlush());
    }

    public function testSensitiveFieldModes(): void
    {
        $field = SensitiveField::create('iban');

        self::assertSame(SensitiveField::MODE_BLUR, $field->getMode());
        self::assertSame('5px', $field->getBlurRadius());

        $field->mask('•', 4);

        self::assertSame(SensitiveField::MODE_MASK, $field->getMode());
        self::assertSame('•', $field->getChar());
        self::assertSame(4, $field->getVisible());
    }

    public function testMergeStateFromCopiesPresentation(): void
    {
        $auto = AutoField::create('name')
            ->setLabel('app.ui.name')
            ->setTranslationDomain('admin')
            ->setTemplate('custom.html.twig')
            ->setPriority(5)
            ->setAttribute('data-foo', 'bar')
            ->setPath('fullName')
        ;

        $field = StringField::create('name')->mergeStateFrom($auto);

        self::assertSame('app.ui.name', $field->getLabel());
        self::assertSame('admin', $field->getTranslationDomain());
        self::assertSame('custom.html.twig', $field->getTemplate());
        self::assertSame(5, $field->getPriority());
        self::assertSame(['data-foo' => 'bar'], $field->getAttributes());
        self::assertSame('fullName', $field->getPath());
    }
}
