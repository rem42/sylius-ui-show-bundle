<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model;

use Rem42\SyliusUiShow\Exception\InvalidShowException;

/**
 * Semantic colors of badges, statuses and progress bars: each theme maps them to its own classes.
 */
enum Color: string
{
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Success = 'success';
    case Info = 'info';
    case Warning = 'warning';
    case Danger = 'danger';

    /**
     * @throws InvalidShowException
     */
    public static function normalize(self|string $color): string
    {
        if ($color instanceof self) {
            return $color->value;
        }

        return (self::tryFrom($color) ?? throw new InvalidShowException(sprintf('Invalid color "%s", expected one of "%s".', $color, implode('", "', array_column(self::cases(), 'value')))))->value;
    }
}
