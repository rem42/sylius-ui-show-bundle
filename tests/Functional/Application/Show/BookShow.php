<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional\Application\Show;

use Rem42\SyliusUiShow\Attribute\AsShow;
use Rem42\SyliusUiShow\Builder\ShowBuilder;
use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\EnumField;
use Rem42\SyliusUiShow\Model\Field\SensitiveField;
use Rem42\SyliusUiShow\Model\Field\StringField;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Rem42\SyliusUiShow\Model\Layout\Column;
use Rem42\SyliusUiShow\Model\Layout\DetailsTable;
use Rem42\SyliusUiShow\Model\Layout\Hook;
use Rem42\SyliusUiShow\Model\Layout\Section;
use Rem42\SyliusUiShow\Model\Layout\Tab;
use Rem42\SyliusUiShow\Model\Layout\Tabs;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Book;

#[AsShow(resourceClass: Book::class, name: 'app_book')]
final class BookShow
{
    public function __invoke(ShowBuilder $show, Book $book): void
    {
        $show->withChildren(
            Section::create('main')->withChildren(
                Column::create('left')->setWidth(8)->withChildren(
                    Card::create('general')
                        ->setLabel('General')
                        ->withChildren(
                            StringField::create('title')->setLabel('Title'),
                            AssociationField::create('author')
                                ->setLabel('Author')
                                ->setRoute('app_author_show'),
                        )
                        ->add('available', 'pages', 'publishedAt', 'tags', 'coverImage'),
                ),
                Column::create(4)->withChildren(
                    Card::create('meta')->setHeader(false)->withChildren(
                        EnumField::create('status')->setLabel('Status')->setBadge(['published' => 'success']),
                        SensitiveField::create('isbn')->setLabel('ISBN')->mask('#', 4),
                    ),
                    Hook::create('extra')->setHookName('app.book.show.extra'),
                ),
            ),
            Tabs::create('details')->withChildren(
                Tab::create('summary')->setLabel('Summary')->withChildren(
                    DetailsTable::create('summary_table')->withChildren(
                        StringField::create('title')->setLabel('Title'),
                    ),
                ),
            ),
        );
    }
}
