<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Guesser;

use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\BooleanField;
use Rem42\SyliusUiShow\Model\Field\DateTimeField;
use Rem42\SyliusUiShow\Model\Field\EnumField;
use Rem42\SyliusUiShow\Model\Field\NumberField;
use Rem42\SyliusUiShow\Model\Field\StringField;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\EnumType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\TypeIdentifier;

/**
 * Guesses the field from PHP types (property types, PHPDoc...) using symfony/property-info and symfony/type-info.
 */
final class PropertyInfoFieldGuesser implements FieldGuesserInterface
{
    public function __construct(
        private readonly ?PropertyTypeExtractorInterface $typeExtractor,
    ) {}

    public function guess(string $class, string $property): ?FieldGuess
    {
        $type = $this->typeExtractor?->getType($class, $property);

        if (null === $type) {
            return null;
        }

        if ($type instanceof NullableType) {
            $type = $type->getWrappedType();
        }

        return $this->guessFromType($type);
    }

    private function guessFromType(Type $type): ?FieldGuess
    {
        if ($type instanceof EnumType) {
            return new FieldGuess(EnumField::class, [], FieldGuess::HIGH_CONFIDENCE);
        }

        if ($type instanceof CollectionType || $type->isIdentifiedBy(TypeIdentifier::ARRAY, TypeIdentifier::ITERABLE)) {
            return new FieldGuess(AssociationField::class, ['display_mode' => AssociationField::MODE_INLINE], FieldGuess::MEDIUM_CONFIDENCE);
        }

        if ($type instanceof ObjectType) {
            if (is_a($type->getClassName(), \DateTimeInterface::class, true)) {
                return new FieldGuess(DateTimeField::class, [], FieldGuess::HIGH_CONFIDENCE);
            }

            return new FieldGuess(AssociationField::class, [], FieldGuess::LOW_CONFIDENCE);
        }

        if ($type->isIdentifiedBy(TypeIdentifier::BOOL, TypeIdentifier::TRUE, TypeIdentifier::FALSE)) {
            return new FieldGuess(BooleanField::class, [], FieldGuess::HIGH_CONFIDENCE);
        }

        if ($type->isIdentifiedBy(TypeIdentifier::INT, TypeIdentifier::FLOAT)) {
            return new FieldGuess(NumberField::class, [], FieldGuess::HIGH_CONFIDENCE);
        }

        if ($type->isIdentifiedBy(TypeIdentifier::STRING)) {
            return new FieldGuess(StringField::class, [], FieldGuess::LOW_CONFIDENCE);
        }

        return null;
    }
}
