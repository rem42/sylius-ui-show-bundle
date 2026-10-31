<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Exception;

final class ShowNotFoundException extends \RuntimeException implements ExceptionInterface
{
    public static function forName(string $name): self
    {
        return new self(sprintf('Show "%s" does not exist. Did you register it with the #[AsShow] attribute?', $name));
    }

    public static function forResource(object $resource): self
    {
        return new self(sprintf('No show supports resources of class "%s". Did you register one with #[AsShow(resourceClass: ...)]?', $resource::class));
    }
}
