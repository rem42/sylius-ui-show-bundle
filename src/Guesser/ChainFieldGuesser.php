<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Guesser;

final class ChainFieldGuesser implements FieldGuesserInterface
{
    /**
     * @param iterable<FieldGuesserInterface> $guessers
     */
    public function __construct(
        private readonly iterable $guessers,
    ) {}

    public function guess(string $class, string $property): ?FieldGuess
    {
        $best = null;

        foreach ($this->guessers as $guesser) {
            $guess = $guesser->guess($class, $property);

            if (null !== $guess && (null === $best || $guess->confidence > $best->confidence)) {
                $best = $guess;
            }

            if (FieldGuess::VERY_HIGH_CONFIDENCE === $best?->confidence) {
                break;
            }
        }

        return $best;
    }
}
