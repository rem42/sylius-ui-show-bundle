# Show definitions

A show definition is an **invokable service** receiving the root `ShowBuilder` and the resource to display.

```php
use Rem42\SyliusUiShow\Attribute\AsShow;
use Rem42\SyliusUiShow\Builder\ShowBuilder;

#[AsShow(resourceClass: Order::class, name: 'app_order', priority: 0)]
final class OrderShow
{
    public function __construct(private readonly OrderPaymentChecker $checker) // regular service: autowiring works
    {
    }

    public function __invoke(ShowBuilder $show, Order $order): void
    {
        // ...
    }
}
```

## `#[AsShow]` arguments

| Argument        | Required | Description                                                                                          |
|-----------------|----------|------------------------------------------------------------------------------------------------------|
| `resourceClass` | yes      | Class **or interface** of the resources this show displays.                                          |
| `name`          | no       | Unique name used to request the show explicitly. Defaults to the service id (the class name).        |
| `priority`      | no       | When several shows support the same class, the highest priority is used by default. Defaults to `0`. |

## How the show of a resource is found

When no name is given (`rem42_sylius_ui_show(order)`, Twig Hooks integration), the registry looks for a show supporting:

1. the exact class of the resource (Doctrine proxies are resolved through their parent class);
2. then each parent class, closest first;
3. then each implemented interface.

This means a show declared for `Sylius\Component\Core\Model\OrderInterface` is used for every order entity, and a more
specific show declared for `App\Entity\Order` takes precedence.

To render another show for the same resource, request it by name: `rem42_sylius_ui_show(order, 'app_order_compact')`.

## Registering without the attribute

Tag the service with `rem42_sylius_ui_show.show` (YAML, XML, PHP config...):

```yaml
services:
    App\Show\OrderShow:
        tags:
            - { name: rem42_sylius_ui_show.show, resource_class: App\Entity\Order, name: app_order, priority: 0 }
```

## Using the builder

Every layout element (and the `ShowBuilder` itself) is a *composite* exposing the same API:

| Method                                 | Description                                                                 |
|----------------------------------------|-----------------------------------------------------------------------------|
| `withChildren(ElementInterface ...$e)` | Adds several children.                                                      |
| `addChild(ElementInterface $e)`        | Adds a child (replaces an existing child with the same name).               |
| `add(string ...$names)`                | Adds fields whose type is [guessed](field-guessing.md).                     |
| `getChild($name)` / `hasChild($name)`  | Accesses a direct child.                                                    |
| `find('a.b.c')`                        | Finds a descendant by a dot-separated path of names.                        |
| `removeChild($name)`                   | Removes a child.                                                            |
| `replaceChild($name, $element)`        | Replaces a child, keeping its position.                                     |

Every element (layout or field) also shares:

| Method                                         | Description                                                                               |
|------------------------------------------------|-------------------------------------------------------------------------------------------|
| `::create(string $name, array $options = [])`  | Named constructor. Names must be unique among siblings.                                   |
| `setLabel(string\|false\|null)`                | Translation key or text; `false` hides the label; `null` generates `<label_prefix><snake_case_name>` (`sylius.ui.` by default). |
| `setTranslationDomain(?string)`                | Translation domain of the label.                                                          |
| `setTemplate(?string)`                         | Overrides the template of this element only.                                              |
| `setPriority(int)`                             | Higher priorities are rendered first (default `0`, insertion order is kept otherwise).    |
| `setEnabled(bool)`                             | Disabled elements are not rendered.                                                       |
| `addClass(string ...)` / `removeClass()`       | Manages the HTML `class` attribute.                                                       |
| `setAttribute($name, $value)`                  | Adds an HTML attribute (`true` renders a boolean attribute, `false`/`null` skips it).      |
| `setOption($key, $value)` / `addOptions([])`   | Free options, readable in templates with `element.option('key')`.                         |

## Adapting a show to the resource

The definition is executed for each rendering, so you can use the resource:

```php
public function __invoke(ShowBuilder $show, Order $order): void
{
    $show->withChildren(
        Card::create('customer')->add('customer', 'shippingAddress'),
        Card::create('payment')
            ->add('paymentState', 'total')
            ->setEnabled($order->isCheckoutCompleted()),
    );
}
```

To alter a show defined elsewhere (e.g. in another bundle), listen to the [`ShowBuildEvent`](customization.md#extending-a-show-with-events).
