<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Accessor;

interface ValueAccessorInterface
{
    /**
     * Reads a value from the resource. "." returns the resource itself; unreachable paths return null.
     */
    public function getValue(mixed $resource, string $path): mixed;
}
