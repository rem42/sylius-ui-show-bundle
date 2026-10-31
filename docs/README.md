# Sylius UI Show Bundle

**Configuration-driven "show" (detail) pages for the [Sylius Stack](https://github.com/Sylius/Stack) and any Symfony application.**

The [Sylius Grid](https://stack.sylius.com/grid/index) describes *index* pages in PHP. This bundle brings the same
philosophy to *show* pages: you describe **what** to display (tabs, cards, columns, fields) in a PHP class, and the
bundle renders it with the markup of your admin UI: `bootstrap` theme for `sylius/bootstrap-admin-ui`, `shadcn` theme for
`rem42/shadcn-admin-ui`.

```php
#[AsShow(resourceClass: Book::class)]
final class BookShow
{
    public function __invoke(ShowBuilder $show, Book $book): void
    {
        $show->withChildren(
            Card::create('general')
                ->withChildren(StringField::create('title'))
                ->add('author', 'publishedAt', 'price', 'enabled'), // types are guessed
        );
    }
}
```

## How it works

```
 #[AsShow] class ──► ShowRegistry ──► ShowFactory ──► ShowBuildEvent ──► AutoFieldResolver ──► TwigShowRenderer
   (definition)      (name / class)    (builds tree)    (extensions)        (guessers)            (templates per type)
```

1. **Definitions** are invokable services tagged through the `#[AsShow]` attribute (like `#[AsGrid]`).
2. The **registry** finds the definition by name, or by resource class (walking parents and interfaces).
3. The **factory** builds a tree of **elements** (layouts and fields) and dispatches a `ShowBuildEvent`.
4. Fields added with `add()` are **guessed** from Doctrine metadata, PHP types and naming conventions.
5. The **renderer** renders each element with the Twig template configured for its **type**.
6. With `sylius/twig-hooks`, the show is **automatically rendered** on Sylius Admin UI show pages.

## Documentation

* [Getting started](getting-started.md)
* [Show definitions](show-definitions.md)
* [Layout elements](layouts.md)
* [Fields](fields.md)
* [Field guessing](field-guessing.md)
* [Admin UI & Twig Hooks integration](sylius-stack.md)
* [Templates, labels & events](customization.md)
* [Twig reference](reference/twig.md)
* [Configuration reference](reference/configuration.md)
