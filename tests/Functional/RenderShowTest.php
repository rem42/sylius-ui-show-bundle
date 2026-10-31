<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional;

use Rem42\SyliusUiShow\Exception\ShowNotFoundException;
use Rem42\SyliusUiShow\Factory\ShowFactoryInterface;
use Rem42\SyliusUiShow\Model\Field\AssociationField;
use Rem42\SyliusUiShow\Model\Field\BooleanField;
use Rem42\SyliusUiShow\Model\Field\DateTimeField;
use Rem42\SyliusUiShow\Model\Field\ImageField;
use Rem42\SyliusUiShow\Model\Field\NumberField;
use Rem42\SyliusUiShow\Model\Layout\Card;
use Rem42\SyliusUiShow\Model\Layout\Column;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Author;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Book;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Ebook;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Status;
use Twig\Environment;

/**
 * @internal
 */
final class RenderShowTest extends KernelTestCase
{
    public function testItBuildsTheShowAndGuessesAutoFields(): void
    {
        $factory = self::getContainer()->get(ShowFactoryInterface::class);
        \assert($factory instanceof ShowFactoryInterface);

        $show = $factory->create($this->createBook());

        self::assertSame('app_book', $show->getName());
        self::assertInstanceOf(BooleanField::class, $show->find('main.left.general.available'));
        self::assertInstanceOf(NumberField::class, $show->find('main.left.general.pages'));
        self::assertInstanceOf(DateTimeField::class, $show->find('main.left.general.publishedAt'));
        self::assertInstanceOf(AssociationField::class, $show->find('main.left.general.tags'));
        self::assertInstanceOf(ImageField::class, $show->find('main.left.general.coverImage'));
    }

    public function testItRendersTheShowOfAResource(): void
    {
        $html = $this->render('{{ rem42_sylius_ui_show(book) }}', ['book' => $this->createBook()]);

        self::assertStringContainsString('data-show="app_book"', $html);
        self::assertStringContainsString('<h3 class="card-title">General</h3>', $html);
        self::assertStringContainsString('Clean Code', $html);
        self::assertStringContainsString('<a href="/authors/7">Robert C. Martin</a>', $html);
        self::assertStringContainsString('col-12 col-md-8', $html);
        self::assertStringContainsString('<span class="badge bg-success-lt">Published</span>', $html);
        self::assertStringContainsString('#########0123', $html);
        self::assertStringContainsString('2008-08-01 00:00:00', $html);
        self::assertStringContainsString('clean, craft', $html);
        self::assertStringContainsString('<img src="https://example.com/cover.png"', $html);
        self::assertStringContainsString('role="tablist"', $html);
        self::assertStringContainsString('sylius.ui.published_at', $html);
        self::assertStringNotContainsString('card-title">sylius.ui.meta', $html);
    }

    public function testColumnClassesDependOnWidthAndBreakpoint(): void
    {
        $html = $this->render('{{ rem42_sylius_ui_show_render(column, null) }}{{ rem42_sylius_ui_show_render(full, null) }}{{ rem42_sylius_ui_show_render(fixed, null) }}', [
            'column' => Column::create(6)->setBreakpoint('lg'),
            'full' => Column::create('full'),
            'fixed' => Column::create(4)->setBreakpoint(''),
        ]);

        self::assertStringContainsString('<div class="col-12 col-lg-6"></div>', $html);
        self::assertStringContainsString('<div class="col-12"></div>', $html);
        self::assertStringContainsString('<div class="col-4"></div>', $html);
    }

    public function testAFlushCardHasNoBodyPadding(): void
    {
        $html = $this->render('{{ rem42_sylius_ui_show_render(card, null) }}', ['card' => Card::create('general')->setFlush()->setBodyClass('bg-light')]);

        self::assertStringContainsString('<div class="card-body p-0 bg-light">', $html);
    }

    public function testItRendersAShowByName(): void
    {
        $html = $this->render('{{ rem42_sylius_ui_show(book, "app_book_compact") }}', ['book' => $this->createBook()]);

        self::assertStringContainsString('data-show="app_book_compact"', $html);
        self::assertStringContainsString('Compact', $html);
    }

    public function testAShowWithoutNameIsNamedAfterItsServiceId(): void
    {
        $html = $this->render(
            '{{ rem42_sylius_ui_show(author, "Tests\\\Rem42\\\SyliusUiShow\\\Functional\\\Application\\\Show\\\AuthorSummaryShow") }}',
            ['author' => new Author(7, 'Robert C. Martin')],
        );

        self::assertStringContainsString('Summary', $html);
        self::assertStringContainsString('Robert C. Martin', $html);
    }

    public function testItResolvesTheShowOfAParentClass(): void
    {
        $html = $this->render('{{ rem42_sylius_ui_show(book) }}', ['book' => new Ebook('Refactoring', false, 448, Status::Draft, new \DateTimeImmutable('1999-07-08'))]);

        self::assertStringContainsString('data-show="app_book"', $html);
        self::assertStringContainsString('Refactoring', $html);
        self::assertStringContainsString('bg-danger-lt', $html);
    }

    public function testItChecksWhetherAShowExists(): void
    {
        self::assertSame('yes|no|no', $this->render(
            '{{ rem42_sylius_ui_show_exists(book) ? "yes" : "no" }}|{{ rem42_sylius_ui_show_exists(other) ? "yes" : "no" }}|{{ rem42_sylius_ui_show_exists(null) ? "yes" : "no" }}',
            ['book' => $this->createBook(), 'other' => new \stdClass()],
        ));
    }

    public function testItThrowsWhenNoShowSupportsTheResource(): void
    {
        $this->expectException(ShowNotFoundException::class);

        $factory = self::getContainer()->get(ShowFactoryInterface::class);
        \assert($factory instanceof ShowFactoryInterface);
        $factory->create(new \stdClass());
    }

    private function createBook(): Book
    {
        return new Book(
            title: 'Clean Code',
            available: true,
            pages: 464,
            status: Status::Published,
            publishedAt: new \DateTimeImmutable('2008-08-01'),
            author: new Author(7, 'Robert C. Martin'),
            tags: ['clean', 'craft'],
            coverImage: 'https://example.com/cover.png',
            isbn: '9780132350123',
        );
    }

    /**
     * @param array<string, mixed> $context
     */
    private function render(string $template, array $context): string
    {
        $twig = self::getContainer()->get('twig');
        \assert($twig instanceof Environment);

        return $twig->createTemplate($template)->render($context);
    }
}
