<?php

declare(strict_types=1);

namespace App\Show;

use App\Entity\Product;
use Rem42\SyliusUiShow\Attribute\AsShow;
use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Model\Color;
use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\BadgeField;
use Rem42\SyliusUiShow\Model\Field\BooleanField;
use Rem42\SyliusUiShow\Model\Field\CollectionField;
use Rem42\SyliusUiShow\Model\Field\DateTimeField;
use Rem42\SyliusUiShow\Model\Field\EnumField;
use Rem42\SyliusUiShow\Model\Field\FileField;
use Rem42\SyliusUiShow\Model\Field\IconField;
use Rem42\SyliusUiShow\Model\Field\ImageField;
use Rem42\SyliusUiShow\Model\Field\NumberField;
use Rem42\SyliusUiShow\Model\Field\ProgressField;
use Rem42\SyliusUiShow\Model\Field\SensitiveField;
use Rem42\SyliusUiShow\Model\Field\StatusField;
use Rem42\SyliusUiShow\Model\Field\StringField;
use Rem42\SyliusUiShow\Model\Field\TwigField;
use Rem42\SyliusUiShow\Model\Field\UrlField;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Rem42\SyliusUiShow\Model\Layout\Column;
use Rem42\SyliusUiShow\Model\Layout\Datagrid;
use Rem42\SyliusUiShow\Model\Layout\DetailsTable;
use Rem42\SyliusUiShow\Model\Layout\Hook;
use Rem42\SyliusUiShow\Model\Layout\Section;
use Rem42\SyliusUiShow\Model\Layout\SideNav;
use Rem42\SyliusUiShow\Model\Layout\SideNavDivider;
use Rem42\SyliusUiShow\Model\Layout\SideNavItem;
use Rem42\SyliusUiShow\Model\Layout\Tab;
use Rem42\SyliusUiShow\Model\Layout\Tabs;
use Rem42\SyliusUiShow\Model\Layout\Template;

/**
 * Showcase of every layout element and field type, rendered by the theme of the kernel (see App\Kernel).
 */
#[AsShow(resourceClass: Product::class, name: 'app_product')]
final class ProductShow
{
    private const STATUS_COLORS = ['draft' => 'secondary', 'published' => 'success', 'archived' => 'danger'];

    public function __invoke(ShowBuilder $show, Product $product): void
    {
        $show->withChildren(
            SideNav::create('dashboard')->withChildren(
                SideNavItem::create('main')->setLabel('Dashboard')->withChildren($this->dashboard()),
                SideNavItem::create('fields')->setLabel('Texts, numbers & dates')->withChildren($this->fields()),
                SideNavItem::create('details')->setLabel('Details & Links')->withChildren(
                    Card::create('tags_card')->setLabel('Tags')->withChildren(
                        BadgeField::create('tags')->setPill()->setOutline()->setDefaultColor(Color::Primary),
                    ),
                    Card::create('links_card')->setLabel('External links')->withChildren(
                        UrlField::create('websiteUrl')->setLabel('Website')->setNewTab(),
                        FileField::create('specs')->setPath('websiteUrl')->setLabel('Download specs'),
                    ),
                ),
                SideNavDivider::create('secret')->setLabel('Secret'),
                SideNavItem::create('sensitive_data')->setLabel('Sensitive data')->withChildren(
                    Card::create('sensitive_card')->setLabel('Sensitive examples')->withChildren(
                        SensitiveField::create('price')->setLabel('Blurred price (hover to reveal)')->blur('8px'),
                        SensitiveField::create('websiteUrl')->setLabel('Masked URL (last 4 visible)')->mask('●', 4),
                        SensitiveField::create('status')->setLabel('Fully masked status')->mask(),
                    ),
                ),
            ),
            Tabs::create('more')->withChildren(
                Tab::create('relations')->setLabel('Relationships')->withChildren($this->relations()),
                Tab::create('details_table')->setLabel('Details table')->withChildren(
                    DetailsTable::create('properties')
                        ->add('name', 'statusEnum', 'isPublished', 'publishedAt')
                        ->withChildren(
                            NumberField::create('price')->setDecimals(2)->setSuffix(' €'),
                            StatusField::create('status')->setAnimated()->setColorMapping(self::STATUS_COLORS),
                            ImageField::create('coverImage')->setLabel('Cover')->setShape(ImageField::SHAPE_ROUNDED),
                            AssociationField::create('variants')->setDisplayMode(AssociationField::MODE_LIST)->setLabelProperty('name'),
                        ),
                ),
                Tab::create('datagrid')->setLabel('Datagrid')->withChildren(
                    Datagrid::create('properties_grid')->add('name', 'price', 'status', 'publishedAt', 'category'),
                ),
                Tab::create('custom_twig')->setLabel('Custom Twig & hooks')->withChildren(
                    Template::create('custom_card')
                        ->setTemplate('custom_card.html.twig')
                        ->setContext(['greeting' => 'Hello from Template::setContext()']),
                    // Hookables configured in config/packages/sylius_twig_hooks.yaml
                    Hook::create('promotion')->setHookName('app.product.show.promotion'),
                ),
            ),
        );
    }

    private function dashboard(): Section
    {
        return Section::create('top_section')->withChildren(
            Column::create('left')->setWidth(6)->withChildren(
                Card::create('identity')
                    ->setLabel('Identity')
                    ->add('name', 'price', 'publishedAt'), // guessed from the Doctrine mapping
            ),
            Column::create(6)->withChildren(
                Card::create('status_card')->setLabel('Status & progress')->withChildren(
                    StatusField::create('status')->setAnimated()->setColorMapping(self::STATUS_COLORS),
                    ProgressField::create('progress')->setLabel('Completion')->setColor(Color::Info),
                    BadgeField::create('isPublished')->setLabel('Public')->setColorMapping(['1' => Color::Success, '' => Color::Warning]),
                    EnumField::create('statusEnum')->setLabel('Status (enum)')->setBadge(self::STATUS_COLORS),
                ),
            ),
            Column::create(12)->withChildren(
                Card::create('media')->setLabel('Visuals')->withChildren(
                    ImageField::create('coverImage')->setLabel('Cover image')->setSize('xl')->setShape(ImageField::SHAPE_ROUNDED)->setLightbox(),
                    StringField::create('category_name')->setPath('category.name')->setLabel('Category'),
                ),
            ),
        );
    }

    private function fields(): Section
    {
        return Section::create('fields_section')->withChildren(
            Column::create(6)->withChildren(
                Card::create('texts')->setLabel('Texts')->withChildren(
                    StringField::create('description')->setMultiline(),
                    StringField::create('excerpt')->setPath('description')->setLabel('Excerpt (30 characters)')->setMaxLength(30),
                ),
                Card::create('booleans')->setLabel('Booleans')->withChildren(
                    BooleanField::create('isPublished')->setLabel('Published (badge)'),
                    BooleanField::create('published_text')->setPath('isPublished')->setLabel('Published (text)')->setBadge(false),
                ),
            ),
            Column::create(6)->withChildren(
                Card::create('numbers')->setLabel('Numbers & dates')->withChildren(
                    NumberField::create('price')->setPrefix('€ ')->setDecimals(2),
                    NumberField::create('stock_number')->setPath('stock')->setLabel('Stock')->setSuffix(' units'),
                    TwigField::create('stock')->setLabel('Stock (TwigField)')->setTemplate('field/stock.html.twig'),
                    DateTimeField::create('updatedAt')->setLabel('Updated at (Paris)')->setFormat('d/m/Y H:i')->setTimezone('Europe/Paris'),
                ),
                Card::create('icons')->setLabel('Icons')->withChildren(
                    IconField::create('icon')->setLabel('Icon (icon set of the theme)'),
                    IconField::create('logo')->setLabel('Inline SVG')->setAllowSvg(),
                ),
            ),
        );
    }

    private function relations(): Section
    {
        return Section::create('relations_section')->withChildren(
            Column::create(3)->withChildren(
                Card::create('category_card')->setLabel('Category (to-one)')->withChildren(
                    AssociationField::create('category')->setLabelProperty('name'),
                ),
            ),
            Column::create(3)->withChildren(
                Card::create('variants_list')->setLabel('Variants as list')->withChildren(
                    AssociationField::create('variants')->setDisplayMode(AssociationField::MODE_LIST)->setLabelProperty('name'),
                ),
            ),
            Column::create(3)->withChildren(
                Card::create('variants_badges')->setLabel('Variants as badges')->withChildren(
                    AssociationField::create('variants')->setDisplayMode(AssociationField::MODE_BADGE)->setLabelProperty('name')->setBadgeColor(Color::Info),
                ),
            ),
            Column::create(3)->withChildren(
                Card::create('variants_links')->setLabel('Variants as links')->withChildren(
                    AssociationField::create('variants')
                        ->setDisplayMode(AssociationField::MODE_INLINE)
                        ->setLabelProperty('name')
                        ->setRoute('app_product_show'),
                ),
            ),
            Column::create(12)->withChildren(
                Card::create('variants_table')->setLabel('Variants as table')->withChildren(
                    CollectionField::create('variants')->setLabel(false)->withColumns(
                        StringField::create('name')->setLabel('Variant name'),
                        StringField::create('product')->setPath('product.name')->setLabel('Parent product'),
                    ),
                ),
            ),
        );
    }
}
