<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Guesser;

interface FieldGuesserInterface
{
    /**
     * @param class-string $class
     */
    public function guess(string $class, string $property): ?FieldGuess;
}
