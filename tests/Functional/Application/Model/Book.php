<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional\Application\Model;

class Book
{
    /**
     * @param list<string> $tags
     */
    public function __construct(
        public string $title,
        public bool $available,
        public int $pages,
        public Status $status,
        public \DateTimeImmutable $publishedAt,
        public ?Author $author = null,
        public array $tags = [],
        public ?string $coverImage = null,
        public ?string $isbn = null,
    ) {}
}
