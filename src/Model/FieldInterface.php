<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model;

interface FieldInterface extends ElementInterface
{
    /**
     * Property path used to read the value from the resource. "." means the resource itself.
     */
    public function getPath(): string;

    public function setPath(string $path): static;

    /**
     * Value displayed when the resolved value is null or empty.
     */
    public function getEmptyValue(): string;

    public function setEmptyValue(string $emptyValue): static;
}
