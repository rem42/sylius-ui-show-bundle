<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Guesser;

use Rem42\SyliusUiShow\Model\Field\BooleanField;
use Rem42\SyliusUiShow\Model\Field\DateTimeField;
use Rem42\SyliusUiShow\Model\Field\ImageField;
use Rem42\SyliusUiShow\Model\Field\UrlField;

/**
 * Guesses the field from naming conventions (createdAt, isEnabled, avatarUrl...).
 */
final class NameFieldGuesser implements FieldGuesserInterface
{
    public function guess(string $class, string $property): ?FieldGuess
    {
        if (1 === preg_match('/(image|avatar|picture|photo|thumbnail|logo)(_?(url|path))?$/i', $property)) {
            return new FieldGuess(ImageField::class, [], FieldGuess::MEDIUM_CONFIDENCE);
        }

        if (1 === preg_match('/(url|link|website|homepage)$/i', $property)) {
            return new FieldGuess(UrlField::class, [], FieldGuess::MEDIUM_CONFIDENCE);
        }

        if (1 === preg_match('/^(is|has|can)([A-Z_])/', $property)) {
            return new FieldGuess(BooleanField::class, [], FieldGuess::MEDIUM_CONFIDENCE);
        }

        if (1 === preg_match('/([a-z0-9]At|_at|Date|_date)$|^date$/', $property)) {
            return new FieldGuess(DateTimeField::class, [], FieldGuess::MEDIUM_CONFIDENCE);
        }

        return null;
    }
}
