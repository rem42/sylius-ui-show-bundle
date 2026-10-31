<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional\Application\Show;

use Rem42\SyliusUiShow\Attribute\AsShow;
use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Book;

/**
 * Alternative show for books, only used when explicitly requested by name.
 */
#[AsShow(resourceClass: Book::class, name: 'app_book_compact', priority: -10)]
final class EbookShow
{
    public function __invoke(ShowBuilder $show, Book $book): void
    {
        $show->withChildren(Card::create('compact')->setLabel('Compact')->add('title'));
    }
}
