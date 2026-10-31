# Templates, labels & events

## Overriding templates

Each element type is rendered with a template configured in `rem42_sylius_ui_show.templates`. Override a type globally:

```yaml
# config/packages/rem42_sylius_ui_show.yaml
rem42_sylius_ui_show:
    templates:
        card: 'admin/show/card.html.twig'
        field_row: 'admin/show/field_row.html.twig'   # wrapper "label + value" of fields
```

or a single element with `setTemplate()`:

```php
Card::create('danger_zone')->setTemplate('admin/book/show/danger_zone.html.twig');
```

Symfony's [bundle template overriding](https://symfony.com/doc/current/bundles/override.html#templates) also works:
`templates/bundles/Rem42SyliusUiShowBundle/<theme>/layout/card.html.twig`.

### Themes

A theme is a directory providing a template for every path of the [Twig reference](reference/twig.md#templates)
(`Configuration::TEMPLATES`). The bundle ships two themes, each one matching an admin UI:

| Theme       | Directory              | Admin UI                                                                  | Default when                         |
|-------------|------------------------|---------------------------------------------------------------------------|--------------------------------------|
| `bootstrap` | `@Rem42SyliusUiShow/bootstrap` | `sylius/bootstrap-admin-ui` (Tabler CSS and JS)                           | otherwise                            |
| `shadcn`    | `@Rem42SyliusUiShow/shadcn`    | `rem42/shadcn-admin-ui` (palette, `badge`/`dot` components, `admin--tabs` controller) | `Rem42ShadcnAdminUiBundle` is enabled |

The themes rely on the assets of their admin UI, which does not need to know about this bundle. With the `shadcn`
theme, Tailwind must scan the templates of this bundle:

```css
@source "../../vendor/rem42/sylius-ui-show-bundle/templates/shadcn";
```

Choose a theme explicitly, or register your own:

```yaml
rem42_sylius_ui_show:
    themes:
        acme: '@AcmeAdmin/show'
    theme: acme
```

Colors given to themes are [semantic](fields.md) (`Color` enum).

### Template context

| Variable   | Available for | Description                                                           |
|------------|---------------|-----------------------------------------------------------------------|
| `element`  | all           | The element being rendered.                                           |
| `resource` | all           | The resource (for collection columns: the current item).              |
| `options`  | all           | Render options (e.g. `render_label`).                                 |
| `field`    | fields        | Alias of `element`.                                                   |
| `value`    | fields        | Value read at the field path.                                         |
| `content`  | `field_row`   | The rendered field value.                                             |

Useful functions: `rem42_sylius_ui_show_render(child, resource)`, `rem42_sylius_ui_show_label(element)|trans`,
`rem42_sylius_ui_show_attributes(element, {class: 'default classes'})`, `value|rem42_sylius_ui_show_string` (see the
[Twig reference](reference/twig.md)).

## Creating a custom element type

1. Create a class extending `AbstractField` (a value) or `CompositeElement` (a layout) and return a new type:

```php
namespace App\Show\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

final class MoneyField extends AbstractField
{
    public function getType(): string
    {
        return 'money';
    }

    public function setCurrency(string $currency): static
    {
        return $this->setOption('currency', $currency);
    }

    public function getCurrency(): string
    {
        $currency = $this->getOption('currency');

        return \is_string($currency) ? $currency : 'EUR';
    }
}
```

2. Map the type to a template:

```yaml
rem42_sylius_ui_show:
    templates:
        money: 'admin/show/field/money.html.twig'
```

```twig
{# admin/show/field/money.html.twig #}
{% if value is null %}{{ field.emptyValue }}{% else %}{{ (value / 100)|format_currency(field.currency) }}{% endif %}
```

3. Use it: `MoneyField::create('total')->setCurrency('USD')`.

## Labels & translations

Elements without a label get a generated translation key: `<label_prefix><snake_case_name>`. With the default
`sylius.ui.` prefix, `createdAt` becomes `sylius.ui.created_at`, which already exists in `sylius/ui-translations` for
most common names.

```yaml
rem42_sylius_ui_show:
    label_prefix: 'app.ui.'
```

```php
StringField::create('isbn')->setLabel('app.ui.isbn')->setTranslationDomain('admin');
Card::create('main')->setLabel(false); // no label / no card header
```

## Extending a show with events

After a definition has been executed, a `Rem42\SyliusUiShow\Event\ShowBuildEvent` is dispatched. Listeners can add,
remove, reorder or change any element, e.g. from another bundle or plugin:

```php
use Rem42\SyliusUiShow\Event\ShowBuildEvent;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class AddLoyaltyCardListener
{
    public function __invoke(ShowBuildEvent $event): void
    {
        if ('app_customer' !== $event->getName()) {
            return;
        }

        $event->getShow()->find('main.sidebar')?->setPriority(10);

        $event->getShow()->addChild(
            Card::create('loyalty')->add('loyaltyPoints')->setPriority(-10),
        );
    }
}
```

`find()` returns an `ElementInterface`: check for `CompositeElementInterface` before adding children to it.

## Services

| Service id                      | Interface                        | Role                                              |
|---------------------------------|----------------------------------|---------------------------------------------------|
| `rem42_sylius_ui_show.registry`           | `ShowRegistryInterface`          | Show definitions by name / resource class.        |
| `rem42_sylius_ui_show.factory`            | `ShowFactoryInterface`           | Builds the element tree of a resource.            |
| `rem42_sylius_ui_show.renderer`           | `ShowRendererInterface`          | Renders an element tree with Twig.                |
| `rem42_sylius_ui_show.template_resolver`  | `TemplateResolverInterface`      | Element type → template.                          |
| `rem42_sylius_ui_show.field_guesser`      | `FieldGuesserInterface`          | Chain of the `rem42_sylius_ui_show.field_guesser` services. |
| `rem42_sylius_ui_show.auto_field_resolver`| `AutoFieldResolverInterface`     | Replaces auto fields by guessed fields.           |
| `rem42_sylius_ui_show.value_accessor`     | `ValueAccessorInterface`         | Null-safe property access.                        |

All of them can be decorated. To render a show from PHP (e.g. a PDF export):

```php
$show = $showFactory->create($book);          // or ->create($book, 'app_book')
$html = $showRenderer->render($show, $book);
```
