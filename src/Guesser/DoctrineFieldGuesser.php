<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Guesser;

use Doctrine\ORM\Mapping\ClassMetadata as OrmClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\MappingException;
use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\BooleanField;
use Rem42\SyliusUiShow\Model\Field\DateTimeField;
use Rem42\SyliusUiShow\Model\Field\EnumField;
use Rem42\SyliusUiShow\Model\Field\NumberField;
use Rem42\SyliusUiShow\Model\Field\StringField;

/**
 * Guesses the field from the Doctrine mapping (associations, column types, enums).
 */
final class DoctrineFieldGuesser implements FieldGuesserInterface
{
    public function __construct(
        private readonly ?ManagerRegistry $registry,
    ) {}

    public function guess(string $class, string $property): ?FieldGuess
    {
        $metadata = $this->getMetadata($class);

        if (null === $metadata) {
            return null;
        }

        if ($metadata->hasAssociation($property)) {
            return new FieldGuess(AssociationField::class, [
                'display_mode' => $metadata->isCollectionValuedAssociation($property) ? AssociationField::MODE_LIST : AssociationField::MODE_LINK,
            ], FieldGuess::HIGH_CONFIDENCE);
        }

        if (!$metadata->hasField($property)) {
            return null;
        }

        if ($metadata instanceof OrmClassMetadata && null !== $this->getEnumType($metadata, $property)) {
            return new FieldGuess(EnumField::class, [], FieldGuess::HIGH_CONFIDENCE);
        }

        return match ($metadata->getTypeOfField($property)) {
            'boolean' => new FieldGuess(BooleanField::class, [], FieldGuess::HIGH_CONFIDENCE),
            'date', 'date_immutable' => new FieldGuess(DateTimeField::class, ['format' => 'Y-m-d'], FieldGuess::HIGH_CONFIDENCE),
            'time', 'time_immutable' => new FieldGuess(DateTimeField::class, ['format' => 'H:i:s'], FieldGuess::HIGH_CONFIDENCE),
            'datetime', 'datetime_immutable', 'datetimetz', 'datetimetz_immutable' => new FieldGuess(DateTimeField::class, [], FieldGuess::HIGH_CONFIDENCE),
            'integer', 'smallint', 'bigint', 'float', 'decimal', 'smallfloat' => new FieldGuess(NumberField::class, [], FieldGuess::HIGH_CONFIDENCE),
            'text' => new FieldGuess(StringField::class, ['multiline' => true], FieldGuess::MEDIUM_CONFIDENCE),
            'string', 'ascii_string' => new FieldGuess(StringField::class, [], FieldGuess::LOW_CONFIDENCE),
            default => null,
        };
    }

    /**
     * @param class-string $class
     *
     * @return ClassMetadata<object>|null
     */
    private function getMetadata(string $class): ?ClassMetadata
    {
        $manager = $this->registry?->getManagerForClass($class);

        if (null === $manager) {
            return null;
        }

        try {
            return $manager->getClassMetadata($class);
        } catch (MappingException) {
            return null;
        }
    }

    /**
     * @param OrmClassMetadata<object> $metadata
     */
    private function getEnumType(OrmClassMetadata $metadata, string $property): ?string
    {
        return $metadata->getFieldMapping($property)->enumType ?? null;
    }
}
