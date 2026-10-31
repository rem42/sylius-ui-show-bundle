# Admin UI & Twig Hooks integration

The bundle follows the [Sylius Stack](https://stack.sylius.com) conventions:

* show pages of `sylius/admin-ui` are composed of [Twig Hooks](https://stack.sylius.com/twig-hooks/getting-started);
* the `bootstrap` and `shadcn` themes use the markup of `sylius/bootstrap-admin-ui` and `rem42/shadcn-admin-ui`;
* definitions are declared with a PHP attribute, like `#[AsGrid]` and `#[AsResource]`.

## Automatic rendering on show pages

When `SyliusTwigHooksBundle` is enabled, the bundle registers a hookable named `rem42_sylius_ui_show` in the
`sylius_admin.common.show.content` hook (priority `-50`, i.e. after the page header):

```
sylius_admin.common.show
└── content
    ├── flashes
    ├── header            (breadcrumbs, title, actions)
    └── rem42_sylius_ui_show        ◄── {{ rem42_sylius_ui_show(resource) }} in .page-body > .container-xl
```

It renders the show supporting the current `resource`, and nothing if there is none. So a complete admin show page only
needs the `Show` operation and a show definition:

```php
#[AsResource(
    section: 'admin',
    templatesDir: '@SyliusAdminUi/crud',
    routePrefix: '/admin',
    operations: [new Index(grid: BookGrid::class), new Show(), /* ... */],
)]
class Book {}

#[AsShow(resourceClass: Book::class)]
final class BookShow { /* ... */ }
```

### Choosing the show of a resource page

Hookables can be configured per resource thanks to Twig Hooks prefixes (`sylius_admin.<resource>.show.content`).
The resource-specific hookable replaces the common one, so the template must be repeated:

```yaml
# config/packages/sylius_twig_hooks.yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.book.show.content':
            rem42_sylius_ui_show:
                template: '@Rem42SyliusUiShow/integration/twig_hooks/show.html.twig'
                configuration:
                    show: 'app_book_compact'   # render a specific show
```

### Disabling it for one resource

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.book.show.content':
            rem42_sylius_ui_show:
                enabled: false
```

### Disabling or moving it globally

```yaml
# config/packages/rem42_sylius_ui_show.yaml
rem42_sylius_ui_show:
    twig_hooks:
        enabled: true                               # false to disable the automatic hookable
        hook: 'sylius_admin.common.show.content'    # hook receiving the hookable
        hookable: 'rem42_sylius_ui_show'                      # hookable name
        priority: -50
```

## Extension points inside a show

Use the [`Hook` element](layouts.md#hook) to let other bundles or your application inject content at a precise place of
a show:

```php
Card::create('sidebar')->withChildren(
    Hook::create('app.book.show.sidebar'),
);
```

```yaml
sylius_twig_hooks:
    hooks:
        'app.book.show.sidebar':
            stock:
                template: 'admin/book/show/stock.html.twig'
```

## Without the Sylius Stack

Nothing depends on the Sylius Stack: use `{{ rem42_sylius_ui_show(resource) }}` in any template, and
[override the templates](customization.md#overriding-templates) to match your design system.
