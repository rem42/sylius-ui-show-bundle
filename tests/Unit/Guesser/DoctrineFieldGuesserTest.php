<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Unit\Guesser;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\FieldMapping;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Rem42\SyliusUiShow\Guesser\DoctrineFieldGuesser;
use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\BooleanField;
use Rem42\SyliusUiShow\Model\Field\DateTimeField;
use Rem42\SyliusUiShow\Model\Field\EnumField;
use Rem42\SyliusUiShow\Model\Field\NumberField;
use Rem42\SyliusUiShow\Model\Field\StringField;

/**
 * @internal
 */
final class DoctrineFieldGuesserTest extends TestCase
{
    private const FIELDS = [
        'enabled' => 'boolean',
        'publishedAt' => 'date_immutable',
        'openingTime' => 'time',
        'createdAt' => 'datetime_immutable',
        'price' => 'decimal',
        'description' => 'text',
        'name' => 'string',
        'state' => 'string',
        'payload' => 'json',
    ];

    /**
     * @param class-string|null    $fieldClass
     * @param array<string, mixed> $options
     */
    #[DataProvider('provideProperties')]
    public function testItGuessesFromTheMapping(string $property, ?string $fieldClass, array $options = []): void
    {
        $guess = $this->createGuesser()->guess(\stdClass::class, $property);

        self::assertSame($fieldClass, $guess?->fieldClass);

        if (null !== $guess) {
            self::assertSame($options, $guess->options);
        }
    }

    /**
     * @return iterable<array{0: string, 1: class-string|null, 2?: array<string, mixed>}>
     */
    public static function provideProperties(): iterable
    {
        yield ['category', AssociationField::class, ['display_mode' => AssociationField::MODE_LINK]];
        yield ['variants', AssociationField::class, ['display_mode' => AssociationField::MODE_LIST]];
        yield ['state', EnumField::class];
        yield ['enabled', BooleanField::class];
        yield ['publishedAt', DateTimeField::class, ['format' => 'Y-m-d']];
        yield ['openingTime', DateTimeField::class, ['format' => 'H:i:s']];
        yield ['createdAt', DateTimeField::class];
        yield ['price', NumberField::class];
        yield ['description', StringField::class, ['multiline' => true]];
        yield ['name', StringField::class];
        yield ['payload', null];
        yield ['unmapped', null];
    }

    public function testItIgnoresClassesWithoutManager(): void
    {
        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn(null);

        self::assertNull((new DoctrineFieldGuesser($registry))->guess(\stdClass::class, 'name'));
        self::assertNull((new DoctrineFieldGuesser(null))->guess(\stdClass::class, 'name'));
    }

    private function createGuesser(): DoctrineFieldGuesser
    {
        $metadata = $this->createStub(ClassMetadata::class);
        $metadata->method('hasAssociation')->willReturnCallback(static fn (string $field): bool => \in_array($field, ['category', 'variants'], true));
        $metadata->method('isCollectionValuedAssociation')->willReturnCallback(static fn (string $field): bool => 'variants' === $field);
        $metadata->method('hasField')->willReturnCallback(static fn (string $field): bool => isset(self::FIELDS[$field]));
        $metadata->method('getTypeOfField')->willReturnCallback(static fn (string $field): ?string => self::FIELDS[$field] ?? null);
        $metadata->method('getFieldMapping')->willReturnCallback(static function (string $field): FieldMapping {
            $mapping = new FieldMapping(self::FIELDS[$field], $field, $field);
            $mapping->enumType = 'state' === $field ? \BackedEnum::class : null;

            return $mapping;
        });

        $manager = $this->createStub(ObjectManager::class);
        $manager->method('getClassMetadata')->willReturn($metadata);

        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($manager);

        return new DoctrineFieldGuesser($registry);
    }
}
