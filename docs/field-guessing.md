# Field guessing

Fields added with `add()` are *auto fields*: their type is guessed when the show is built.

```php
Card::create('general')->add('name', 'enabled', 'createdAt', 'category', 'price');
```

Auto fields can be configured like any element, the configuration is kept on the guessed field:

```php
$card->add('createdAt');
$card->getChild('createdAt')?->setLabel('app.ui.created_at');
```

## Guessers

Each guesser returns a `FieldGuess` (field class, options and a **confidence**). The guess with the highest confidence
wins, like Symfony Form type guessers.

| Guesser                    | Source                                  | Examples                                                                                              |
|----------------------------|-----------------------------------------|-------------------------------------------------------------------------------------------------------|
| `DoctrineFieldGuesser`     | Doctrine mapping (if Doctrine is installed) | to-one → `AssociationField` (link), to-many → `AssociationField` (list), `boolean`, `date*`, `time*`, numbers, `text` (multiline), enums |
| `PropertyInfoFieldGuesser` | PHP types / PHPDoc (`property_info`)   | `bool`, `int`/`float`, `DateTimeInterface`, enums, arrays/collections, objects, `string` (low confidence) |
| `NameFieldGuesser`         | Naming conventions (medium confidence) | `*image`/`*avatar`/`*logo`/`*photo`... (optionally followed by `Url`/`Path`) → image, `*url`/`*link`/`*website`/`*homepage` → url, `is*`/`has*`/`can*` → boolean, `*At`/`*_at`/`*Date`/`*_date`/`date` → datetime |

Anything that cannot be guessed, and nested paths (`customer.email`), fall back to `StringField`.

## Adding a guesser

Implement `FieldGuesserInterface`; with autoconfiguration the service is tagged `rem42_sylius_ui_show.field_guesser`
automatically:

```php
use Rem42\SyliusUiShow\Guesser\FieldGuess;
use Rem42\SyliusUiShow\Guesser\FieldGuesserInterface;
use Rem42\SyliusUiShow\Model\Field\NumberField;

final class MoneyFieldGuesser implements FieldGuesserInterface
{
    public function guess(string $class, string $property): ?FieldGuess
    {
        if (!str_ends_with($property, 'Total') && 'total' !== $property) {
            return null;
        }

        return new FieldGuess(NumberField::class, ['decimals' => 2, 'suffix' => ' €'], FieldGuess::HIGH_CONFIDENCE);
    }
}
```

`FieldGuess::VERY_HIGH_CONFIDENCE` stops the chain immediately.
