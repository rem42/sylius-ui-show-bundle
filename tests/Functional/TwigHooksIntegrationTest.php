<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Book;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Status;
use Twig\Environment;

/**
 * @internal
 */
final class TwigHooksIntegrationTest extends KernelTestCase
{
    public function testTheShowIsRenderedInTheAdminShowContentHook(): void
    {
        $html = $this->render("{% hook 'sylius_admin.common.show.content' with { resource: book } %}", ['book' => $this->createBook()]);

        self::assertStringContainsString('<div class="page-body">', $html);
        self::assertStringContainsString('data-show="app_book"', $html);
    }

    public function testNothingIsRenderedForResourcesWithoutShow(): void
    {
        $html = $this->render("{% hook 'sylius_admin.common.show.content' with { resource: resource } %}", ['resource' => new \stdClass()]);

        self::assertStringNotContainsString('page-body', $html);
    }

    public function testTheShowNameCanBeConfiguredPerResource(): void
    {
        $html = $this->render(
            "{% hook ['sylius_admin.book.show.content', 'sylius_admin.common.show.content'] with { resource: book } %}",
            ['book' => $this->createBook()],
        );

        self::assertStringContainsString('data-show="app_book_compact"', $html);
    }

    public function testTheHookElementRendersHookables(): void
    {
        $html = $this->render('{{ rem42_sylius_ui_show(book) }}', ['book' => $this->createBook()]);

        self::assertStringContainsString('<p class="hookable-extra">Extra for Clean Code in extra</p>', $html);
    }

    private function createBook(): Book
    {
        return new Book('Clean Code', true, 464, Status::Published, new \DateTimeImmutable('2008-08-01'));
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
