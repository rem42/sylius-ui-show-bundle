<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional\Application\Show;

use Rem42\SyliusUiShow\Attribute\AsShow;
use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Model\Color;
use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\BadgeField;
use Rem42\SyliusUiShow\Model\Field\CollectionField;
use Rem42\SyliusUiShow\Model\Field\FileField;
use Rem42\SyliusUiShow\Model\Field\IconField;
use Rem42\SyliusUiShow\Model\Field\NumberField;
use Rem42\SyliusUiShow\Model\Field\ProgressField;
use Rem42\SyliusUiShow\Model\Field\SensitiveField;
use Rem42\SyliusUiShow\Model\Field\StatusField;
use Rem42\SyliusUiShow\Model\Field\StringField;
use Rem42\SyliusUiShow\Model\Field\TwigField;
use Rem42\SyliusUiShow\Model\Field\UrlField;
use Rem42\SyliusUiShow\Model\Layout\Datagrid;
use Rem42\SyliusUiShow\Model\Layout\SideNav;
use Rem42\SyliusUiShow\Model\Layout\SideNavDivider;
use Rem42\SyliusUiShow\Model\Layout\SideNavItem;
use Rem42\SyliusUiShow\Model\Layout\Template;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Author;

/**
 * Uses every element type, to make sure all default templates render.
 */
#[AsShow(resourceClass: Author::class, name: 'kitchen_sink')]
final class KitchenSinkShow
{
    public function __invoke(ShowBuilder $show, Author $author): void
    {
        $show->withChildren(
            SideNav::create('menu')->withChildren(
                SideNavDivider::create('first_divider')->setLabel('Divider'),
                SideNavItem::create('general')->setLabel('General')->withChildren(
                    Datagrid::create('grid')->withChildren(
                        StringField::create('name')->setLabel('Name')->setMaxLength(3),
                        NumberField::create('id')->setLabel('Id')->setDecimals(2)->setPrefix('#'),
                        BadgeField::create('badges')->setPath('.')->setLabel('Badges')->setColorMapping(['Jane' => Color::Success])->setPill(),
                        StatusField::create('status')->setPath('name')->setLabel('Status')->setAnimated(),
                        ProgressField::create('progress')->setPath('id')->setLabel('Progress')->setMax(4),
                        IconField::create('icon')->setPath('name')->setLabel('Icon'),
                        UrlField::create('url')->setPath('name')->setLabel('Url')->setNewTab(),
                        FileField::create('file')->setPath('name')->setLabel('File'),
                        SensitiveField::create('secret')->setPath('name')->setLabel('Secret')->blur('3px'),
                        TwigField::create('custom')->setPath('name')->setLabel('Custom')->setTemplate('custom_field.html.twig'),
                        StringField::create('missing')->setPath('unknown?')->setLabel('Missing')->setEnabled(false),
                    ),
                ),
                SideNavItem::create('more')->setLabel('More')->withChildren(
                    CollectionField::create('books')->setPath('books')->setLabel('Books')->withColumns(
                        StringField::create('title')->setLabel('Book title'),
                    ),
                    AssociationField::create('friends')->setPath('books')->setLabel('Friends')->setDisplayMode(AssociationField::MODE_BADGE)->setLabelProperty('title'),
                    AssociationField::create('friends_list')->setPath('books')->setLabel('Friend list')->setDisplayMode(AssociationField::MODE_LIST)->setLabelProperty('title'),
                    Template::create('custom_template')->setTemplate('custom_template.html.twig')->setContext(['greeting' => 'Hello']),
                ),
            ),
        );
    }
}
