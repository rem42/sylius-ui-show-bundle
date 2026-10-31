<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Unit\Guesser;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rem42\SyliusUiShow\Guesser\AutoFieldResolver;
use Rem42\SyliusUiShow\Guesser\ChainFieldGuesser;
use Rem42\SyliusUiShow\Guesser\FieldGuess;
use Rem42\SyliusUiShow\Guesser\FieldGuesserInterface;
use Rem42\SyliusUiShow\Guesser\NameFieldGuesser;
use Rem42\SyliusUiShow\Guesser\PropertyInfoFieldGuesser;
use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\BooleanField;
use Rem42\SyliusUiShow\Model\Field\DateTimeField;
use Rem42\SyliusUiShow\Model\Field\EnumField;
use Rem42\SyliusUiShow\Model\Field\ImageField;
use Rem42\SyliusUiShow\Model\Field\NumberField;
use Rem42\SyliusUiShow\Model\Field\StringField;
use Rem42\SyliusUiShow\Model\Field\UrlField;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

/**
 * @internal
 */
final class FieldGuesserTest extends TestCase
{
    /**
     * @param class-string|null $expected
     */
    #[DataProvider('provideNames')]
    public function testNameGuesser(string $property, ?string $expected): void
    {
        self::assertSame($expected, (new NameFieldGuesser())->guess(\stdClass::class, $property)?->fieldClass);
    }

    /**
     * @return iterable<array{string, class-string|null}>
     */
    public static function provideNames(): iterable
    {
        yield ['createdAt', DateTimeField::class];
        yield ['created_at', DateTimeField::class];
        yield ['birthDate', DateTimeField::class];
        yield ['status', null];
        yield ['format', null];
        yield ['candidate', null];
        yield ['isEnabled', BooleanField::class];
        yield ['hasStock', BooleanField::class];
        yield ['isbn', null];
        yield ['issue', null];
        yield ['avatar', ImageField::class];
        yield ['coverImageUrl', ImageField::class];
        yield ['website', UrlField::class];
        yield ['name', null];
    }

    /**
     * @param class-string $expected
     */
    #[DataProvider('provideProperties')]
    public function testPropertyInfoGuesser(string $property, string $expected, int $confidence): void
    {
        $guess = (new PropertyInfoFieldGuesser(new ReflectionExtractor()))->guess(GuessedResource::class, $property);

        self::assertNotNull($guess);
        self::assertSame($expected, $guess->fieldClass);
        self::assertSame($confidence, $guess->confidence);
    }

    /**
     * @return iterable<array{string, class-string, int}>
     */
    public static function provideProperties(): iterable
    {
        yield ['name', StringField::class, FieldGuess::LOW_CONFIDENCE];
        yield ['enabled', BooleanField::class, FieldGuess::HIGH_CONFIDENCE];
        yield ['price', NumberField::class, FieldGuess::HIGH_CONFIDENCE];
        yield ['createdAt', DateTimeField::class, FieldGuess::HIGH_CONFIDENCE];
        yield ['state', EnumField::class, FieldGuess::HIGH_CONFIDENCE];
        yield ['tags', AssociationField::class, FieldGuess::MEDIUM_CONFIDENCE];
        yield ['parent', AssociationField::class, FieldGuess::LOW_CONFIDENCE];
    }

    public function testPropertyInfoGuesserWithoutExtractor(): void
    {
        self::assertNull((new PropertyInfoFieldGuesser(null))->guess(GuessedResource::class, 'name'));
    }

    public function testChainKeepsTheMostConfidentGuess(): void
    {
        $chain = new ChainFieldGuesser([
            new PropertyInfoFieldGuesser(new ReflectionExtractor()),
            new NameFieldGuesser(),
        ]);

        // string type (low) vs name convention (medium)
        self::assertSame(ImageField::class, $chain->guess(GuessedResource::class, 'avatarUrl')?->fieldClass);
        // bool type (high) wins
        self::assertSame(BooleanField::class, $chain->guess(GuessedResource::class, 'enabled')?->fieldClass);
        self::assertNull($chain->guess(GuessedResource::class, 'unknown'));
    }

    public function testChainStopsAtVeryHighConfidence(): void
    {
        $never = $this->createMock(FieldGuesserInterface::class);
        $never->expects(self::never())->method('guess');

        $chain = new ChainFieldGuesser([
            new class implements FieldGuesserInterface {
                public function guess(string $class, string $property): ?FieldGuess
                {
                    return new FieldGuess(UrlField::class, [], FieldGuess::VERY_HIGH_CONFIDENCE);
                }
            },
            $never,
        ]);

        self::assertSame(UrlField::class, $chain->guess(GuessedResource::class, 'name')?->fieldClass);
    }

    public function testAutoFieldResolverReplacesAutoFieldsRecursively(): void
    {
        $card = Card::create('root')->withChildren(
            Card::create('nested')->add('enabled')->withChildren(StringField::create('kept')),
        )->add('createdAt', 'unknown', 'parent.name');

        $card->getChild('createdAt')?->setLabel('Created');

        (new AutoFieldResolver(new ChainFieldGuesser([
            new PropertyInfoFieldGuesser(new ReflectionExtractor()),
            new NameFieldGuesser(),
        ])))->resolve($card, GuessedResource::class);

        self::assertSame(['nested', 'createdAt', 'unknown', 'parent.name'], array_keys($card->getAllChildren()));
        self::assertInstanceOf(BooleanField::class, $card->find('nested.enabled'));
        self::assertInstanceOf(StringField::class, $card->find('nested.kept'));
        self::assertInstanceOf(DateTimeField::class, $card->getChild('createdAt'));
        self::assertSame('Created', $card->getChild('createdAt')->getLabel());
        self::assertInstanceOf(StringField::class, $card->getChild('unknown'));
        self::assertInstanceOf(StringField::class, $card->getChild('parent.name'));
    }
}

enum GuessedState: string
{
    case New = 'new';
}

final class GuessedResource
{
    public string $name = '';

    public bool $enabled = false;

    public float $price = 0.0;

    public ?\DateTimeImmutable $createdAt = null;

    public GuessedState $state = GuessedState::New;

    /** @var list<string> */
    public array $tags = [];

    public ?self $parent = null;

    public string $avatarUrl = '';
}
