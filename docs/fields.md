# Fields

Fields live in `Rem42\SyliusUiShow\Model\Field`. A field reads a value from the resource with the
[PropertyAccess](https://symfony.com/doc/current/components/property_access.html) component and renders it.

```php
StringField::create('title');                          // reads $resource->getTitle() / ->title / ->isTitle()...
StringField::create('customer')->setPath('customer.email');
StringField::create('self')->setPath('.');             // the resource itself
```

Unreachable paths (e.g. `customer.email` when there is no customer) render the **empty value** (`-` by default,
see `setEmptyValue()`) instead of throwing.

When rendered outside of a `DetailsTable` or a `Datagrid`, a field is wrapped with its label (`field_row` template).
Use `setLabel(false)` to render the bare value.

## Common API

| Method                         | Description                                                                   |
|--------------------------------|-------------------------------------------------------------------------------|
| `setPath(string $path)`        | Property path, defaults to the name. `.` targets the resource itself.         |
| `setEmptyValue(string $value)` | Text displayed for `null`/empty values (default `-`).                         |
| + every [element method](show-definitions.md#using-the-builder) (`setLabel`, `setTemplate`, `setPriority`...). | |

## Available fields

| Field              | Type          | Options / methods                                                                                                  |
|--------------------|---------------|--------------------------------------------------------------------------------------------------------------------|
| `StringField`      | `string`      | `setMultiline()` (converts new lines to `<br>`), `setMaxLength(?int)` (truncates).                                 |
| `NumberField`      | `number`      | `setDecimals(int)`, `setPrefix(?string)`, `setSuffix(?string)`.                                                    |
| `BooleanField`     | `boolean`     | `setBadge(bool)` — green/red badge (default) or plain "Yes"/"No" (`sylius.ui.yes` / `sylius.ui.no`).               |
| `DateTimeField`    | `datetime`    | `setFormat(string)` (default `Y-m-d H:i:s`), `setTimezone(?string)`.                                               |
| `EnumField`        | `enum`        | `setChoices([value => label])`, `setTranslatable()`, `setBadge(bool\|[value => color])`.                           |
| `AssociationField` | `association` | `setDisplayMode(link\|list\|inline\|badge)`, `setLabelProperty(path)`, `setRoute(name, [param => path])`, `setBadgeColor()`. |
| `CollectionField`  | `collection`  | `withColumns(FieldInterface ...)` — renders a table, each column being a field resolved against every item. `setEmptyMessage()`. |
| `BadgeField`       | `badge`       | `setColorMapping([value => color])`, `setDefaultColor()`, `setOutline()`, `setPill()`. Iterable values render one badge per item. |
| `StatusField`      | `status`      | `setColorMapping([value => color])`, `setAnimated()`.                                                              |
| `ProgressField`    | `progress`    | `setMax(int\|float)` (default 100), `setColor(?Color)`.                                                           |
| `ImageField`       | `image`       | `setShape(square\|rounded\|circle)`, `setSize(xs\|sm\|md\|lg\|xl)`, `setLightbox()`, `setBasePath(?string)`.        |
| `IconField`        | `icon`        | Renders `<i class="{value}">`. `setAllowSvg()` outputs raw `<svg>` values (**not escaped**: trusted data only).     |
| `UrlField`         | `url`         | `setNewTab()`, `setLabelProperty(path)`. `javascript:`/`data:`/`vbscript:` URLs are rendered as text.                |
| `FileField`        | `file`        | Same as `UrlField`, rendered as a download button.                                                                 |
| `SensitiveField`   | `sensitive`   | `blur(radius = '5px')` (revealed on hover/focus, default) or `mask(char = '*', visible = 0)`.                      |
| `TwigField`        | `twig`        | Renders the value with your template (required): `TwigField::create('stock')->setTemplate('admin/stock.html.twig')`. |

Colors are semantic, so that every theme can render them: `Rem42\SyliusUiShow\Model\Color` cases or their values
(`primary`, `secondary`, `success`, `info`, `warning`, `danger`). Any other value throws an `InvalidShowException`.

## Examples

```php
EnumField::create('state')
    ->setChoices(['new' => 'app.ui.new', 'fulfilled' => 'app.ui.fulfilled'])
    ->setTranslatable()
    ->setBadge(['new' => Color::Info, 'fulfilled' => Color::Success]);

AssociationField::create('customer')
    ->setLabelProperty('fullName')
    ->setRoute('sylius_admin_customer_show', ['id' => 'id']);

AssociationField::create('taxons')
    ->setDisplayMode(AssociationField::MODE_BADGE)
    ->setLabelProperty('name');

CollectionField::create('items')->withColumns(
    StringField::create('productName')->setLabel('sylius.ui.product'),
    NumberField::create('quantity'),
    NumberField::create('total')->setDecimals(2)->setSuffix(' €'),
);

SensitiveField::create('iban')->mask('•', 4);
```

## Custom value template

Any field can use its own template. The template receives `value`, `field`, `resource` and `options`:

```php
StringField::create('stock')->setTemplate('admin/product/show/stock.html.twig');
```

```twig
{# admin/product/show/stock.html.twig #}
<span class="badge bg-{{ value > 0 ? 'success' : 'danger' }}-lt">{{ value }}</span>
```

To create a reusable field type, see [custom element types](customization.md#creating-a-custom-element-type).
