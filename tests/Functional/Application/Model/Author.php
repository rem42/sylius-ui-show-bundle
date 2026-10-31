<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional\Application\Model;

final class Author implements \Stringable
{
    public function __construct(
        public int $id,
        public string $name,
        /** @var list<object> */
        public array $books = [],
    ) {}

    public function __toString(): string
    {
        return $this->name;
    }
}
