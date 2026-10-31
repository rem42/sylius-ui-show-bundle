<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional\Application\Show;

use Rem42\SyliusUiShow\Attribute\AsShow;
use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Author;

/**
 * Show registered without a name: it is named after its service id.
 */
#[AsShow(resourceClass: Author::class, priority: -10)]
final class AuthorSummaryShow
{
    public function __invoke(ShowBuilder $show, Author $author): void
    {
        $show->withChildren(Card::create('summary')->setLabel('Summary')->add('name'));
    }
}
