<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Guesser;

use Rem42\SyliusUiShow\Model\AbstractField;

/**
 * Result of a field guess. When several guessers answer, the guess with the highest confidence wins.
 */
final class FieldGuess
{
    public const VERY_HIGH_CONFIDENCE = 3;

    public const HIGH_CONFIDENCE = 2;

    public const MEDIUM_CONFIDENCE = 1;

    public const LOW_CONFIDENCE = 0;

    /**
     * @param class-string<AbstractField> $fieldClass
     * @param array<string, mixed>        $options
     */
    public function __construct(
        public readonly string $fieldClass,
        public readonly array $options = [],
        public readonly int $confidence = self::MEDIUM_CONFIDENCE,
    ) {}

    public function createField(string $name): AbstractField
    {
        return $this->fieldClass::create($name, $this->options);
    }
}
