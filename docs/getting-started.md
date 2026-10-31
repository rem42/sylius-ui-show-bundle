# Getting started

## Requirements

* PHP 8.4+
* Symfony 7.4 or 8.x
* Optional: `sylius/admin-ui`, `sylius/bootstrap-admin-ui` or `rem42/shadcn-admin-ui`, and `sylius/twig-hooks` (the [Sylius Stack](https://stack.sylius.com))
* Optional: `doctrine/doctrine-bundle` for Doctrine-based field guessing

## Installation

```bash
composer require rem42/sylius-ui-show-bundle
```

Register the bundle if you are not using Symfony Flex:

```php
// config/bundles.php
return [
    // ...
    Rem42\SyliusUiShow\Symfony\Rem42SyliusUiShowBundle::class => ['all' => true],
];
```

No configuration is required. See the [configuration reference](reference/configuration.md) for the available options.

## Your first show

Create an invokable class and add the `#[AsShow]` attribute. With autoconfiguration enabled (the Symfony default),
nothing else is needed.

```php
// src/Show/BookShow.php
namespace App\Show;

use App\Entity\Book;
use Rem42\SyliusUiShow\Attribute\AsShow;
use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Model\Field\DateTimeField;
use Rem42\SyliusUiShow\Model\Field\StringField;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Rem42\SyliusUiShow\Model\Layout\Column;
use Rem42\SyliusUiShow\Model\Layout\Section;

#[AsShow(resourceClass: Book::class, name: 'app_book')]
final class BookShow
{
    public function __invoke(ShowBuilder $show, Book $book): void
    {
        $show->withChildren(
            Section::create('main')->withChildren(
                Column::create(8)->withChildren(
                    Card::create('general')
                        ->setLabel('app.ui.general')
                        ->withChildren(
                            StringField::create('title')->setLabel('app.ui.title'),
                            StringField::create('description')->setMultiline(),
                        ),
                ),
                Column::create(4)->withChildren(
                    Card::create('dates')
                        ->withChildren(DateTimeField::create('createdAt')->setFormat('d/m/Y'))
                        ->add('updatedAt'), // the field type is guessed
                ),
            ),
        );
    }
}
```

The second argument is the resource being displayed: use it to adapt the show (e.g. hide a card when the order is
not paid yet).

## Rendering

### With the Sylius Stack (Admin UI + Twig Hooks)

Nothing to do: the show is rendered in the `sylius_admin.common.show.content` hook of every show page whose resource
has a show definition. Just enable the `Show` operation on your resource:

```php
#[AsResource(section: 'admin', templatesDir: '@SyliusAdminUi/crud', routePrefix: '/admin', operations: [
    new Show(),
    // ...
])]
class Book {}
```

See [Admin UI & Twig Hooks integration](sylius-stack.md).

### In any Twig template

```twig
{{ rem42_sylius_ui_show(book) }}              {# show resolved from the resource class #}
{{ rem42_sylius_ui_show(book, 'app_book') }}  {# explicit show name #}
```

The templates come from a [theme](customization.md#themes) (`rem42_sylius_ui_show.theme`): `bootstrap` (Tabler classes, for
`sylius/bootstrap-admin-ui`) or `shadcn` (Tailwind CSS, for `rem42/shadcn-admin-ui`, selected automatically when its
bundle is enabled).
