<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Rem42\SyliusUiShow\Functional\Application\Model\Author;
use Twig\Environment;

/**
 * @internal
 */
final class KitchenSinkTest extends KernelTestCase
{
    public function testEveryElementTypeRenders(): void
    {
        $twig = self::getContainer()->get('twig');
        \assert($twig instanceof Environment);

        $author = new Author(3, 'Jane', [
            (object) ['title' => 'First book'],
            (object) ['title' => 'Second book'],
        ]);

        $html = $twig->createTemplate('{{ rem42_sylius_ui_show(author, "kitchen_sink") }}')->render(['author' => $author]);

        // side nav: the divider is not the active item
        self::assertStringContainsString('<div class="hr-text my-3">Divider</div>', $html);
        self::assertMatchesRegularExpression('/list-group-item-action active"[^>]*>General</', $html);
        // datagrid
        self::assertStringContainsString('<div class="datagrid-title">Name</div>', $html);
        self::assertStringContainsString('Jan…', $html);
        self::assertStringContainsString('#3.00', $html);
        self::assertStringContainsString('<span class="badge bg-success text-success-fg badge-pill">Jane</span>', $html);
        self::assertStringContainsString('status-dot status-dot-animated', $html);
        self::assertStringContainsString('style="width: 75%"', $html);
        self::assertStringContainsString('<i class="Jane" aria-hidden="true"></i>', $html);
        self::assertStringContainsString('<a href="Jane" target="_blank" rel="noopener noreferrer">Jane</a>', $html);
        self::assertStringContainsString('btn btn-sm', $html);
        self::assertStringContainsString('--rem42-sylius-ui-show-blur: 3px;', $html);
        self::assertStringContainsString('<strong class="custom-field">JANE</strong>', $html);
        self::assertStringNotContainsString('Missing', $html);
        // collection & associations
        self::assertStringContainsString('<th>Book title</th>', $html);
        self::assertStringContainsString('<td>Second book</td>', $html);
        self::assertStringContainsString('<span class="badge bg-primary-lt me-1">First book</span>', $html);
        self::assertStringContainsString('<li>Second book</li>', $html);
        // template
        self::assertStringContainsString('<p class="custom-template">Hello Jane (custom_template)</p>', $html);
    }

    public function testUnsafeUrlsAreNotLinked(): void
    {
        $twig = self::getContainer()->get('twig');
        \assert($twig instanceof Environment);

        $html = $twig->createTemplate('{{ rem42_sylius_ui_show(author, "kitchen_sink") }}')->render(['author' => new Author(1, 'javascript:alert(1)')]);

        self::assertStringNotContainsString('href="javascript:', $html);
    }
}
