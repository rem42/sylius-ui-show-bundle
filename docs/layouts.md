# Layout elements

Layout elements live in `Rem42\SyliusUiShow\Model\Layout` and can be nested freely. All of them accept children through
`withChildren()`, `addChild()` and `add()` (except `SideNavDivider` and `Hook`).

| Element        | Type (template key) | Markup (`bootstrap` theme; `shadcn` uses the Tailwind equivalents)        |
|----------------|---------------------|---------------------------------------------------------------------------|
| `Section`      | `section`           | Bootstrap `.row`. Non-column children are wrapped in `.col-12`.           |
| `Column`       | `column`            | Responsive `.col-12 .col-{breakpoint}-{width}`.                           |
| `Card`         | `card`              | Tabler card with an optional header. Children are rendered in its body.   |
| `Tabs` / `Tab` | `tabs` / `tab`      | Bootstrap tabs inside a card.                                             |
| `SideNav`      | `side_nav`          | Vertical list-group navigation switching panes (`SideNavItem`).           |
| `SideNavItem`  | `side_nav_item`     | A pane of a `SideNav`.                                                    |
| `SideNavDivider` | `side_nav_divider` | A labelled separator in a `SideNav`.                                     |
| `DetailsTable` | `details_table`     | Key/value table: label in `<th>`, value in `<td>`.                        |
| `Datagrid`     | `datagrid`          | Tabler `.datagrid`: responsive grid of title/value pairs.                 |
| `Template`     | `template`          | Renders your own Twig template.                                           |
| `Hook`         | `hook`              | Renders a Sylius Twig Hook (requires `sylius/twig-hooks`).                |

## Section & Column

```php
Section::create('main')->withChildren(
    Column::create(8)->withChildren(/* ... */),       // name generated, width 8
    Column::create('sidebar')->setWidth(4)->withChildren(/* ... */),
);
```

* `setWidth(int)` — 1 to 12 (default 12).
* `setBreakpoint(string)` — breakpoint from which the width applies (default `md`: full width on phones). Use `''` to
  apply the width on every screen size (`col-8`).

## Card

```php
Card::create('general')
    ->setLabel('app.ui.general')   // header title
    ->setHeader(false)             // or hide the header
    ->setFlush()                   // body without padding, e.g. around a DetailsTable
    ->withChildren(/* fields, columns... */);
```

`setBodyClass(string)` adds classes to the body: they are specific to the theme (Bootstrap or Tailwind utilities).

Columns inside a card are laid out in a row, other children take the full width.

## Tabs

```php
Tabs::create('details')->withChildren(
    Tab::create('general')->setLabel('app.ui.general')->withChildren(/* ... */),
    Tab::create('history')->setLabel('app.ui.history')->withChildren(/* ... */),
);
```

`Tabs::addTab(Tab $tab)` is a typed alternative to `addChild()`.

## Side navigation

```php
SideNav::create('menu')->withChildren(
    SideNavItem::create('general')->withChildren(/* ... */),
    SideNavDivider::create('advanced')->setLabel('app.ui.advanced'),
    SideNavItem::create('seo')->withChildren(/* ... */),
);
```

The navigation is sticky on large screens; the first item is active.

## DetailsTable & Datagrid

Both render their **fields** with the label outside of the value:

```php
DetailsTable::create('summary')
    ->setLabelWidth('30%') // default 230px
    ->add('code', 'name', 'enabled', 'createdAt');

Datagrid::create('meta')->add('createdAt', 'updatedAt', 'author');
```

## Template

Renders any Twig template. The template receives `element`, `resource`, `options` and the variables of `setContext()`:

```php
Template::create('stats')
    ->setTemplate('admin/book/show/stats.html.twig')
    ->setContext(['period' => 30]);
```

```twig
{# admin/book/show/stats.html.twig #}
<div class="card mb-3"><div class="card-body">
    {{ resource.title }} sold {{ resource.salesCount(period) }} copies in {{ period }} days.
</div></div>
```

`Template` is also a composite: render its children with `rem42_sylius_ui_show_render(child, resource)`.

## Hook

Exposes an extension point inside your show with [Sylius Twig Hooks](https://stack.sylius.com/twig-hooks/getting-started):

```php
Hook::create('app.book.show.sidebar'); // hook name defaults to the element name
Hook::create('sidebar')->setHookName('app.book.show.sidebar');
```

Hookables receive `resource` and `element` in their context (`hookable_metadata.context.resource`).
