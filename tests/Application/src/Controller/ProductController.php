<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Variant;
use App\Enum\ProductStatus;
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    /** Icon of the product in the icon set of each theme: Tabler Icons webfont classes or UX Icons names. */
    private const ICONS = ['bootstrap' => 'ti ti-rocket', 'shadcn' => 'lucide:rocket'];

    private const LOGO = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>';

    #[Route('/', name: 'app_product_show')]
    public function __invoke(Request $request): Response
    {
        /** @var string $theme */
        $theme = $this->getParameter('app.theme');

        // In-memory product: the demo does not need a database.
        $product = (new Product())
            ->setId(42)
            ->setName('Super Gadget 42')
            ->setDescription("A gadget that does everything.\nShips with a charger and a carrying case.")
            ->setIsPublished(true)
            ->setPrice(50.42)
            ->setStock(1234)
            ->setPublishedAt(new \DateTimeImmutable('-1 week'))
            ->setUpdatedAt(new \DateTimeImmutable('-2 hours'))
            ->setStatus('published')
            ->setStatusEnum(ProductStatus::PUBLISHED)
            ->setProgress(85)
            ->setCoverImage('https://placehold.co/600x400/png')
            ->setTags(['New', 'Feature', 'Best-Seller'])
            ->setWebsiteUrl('https://example.com')
            ->setIcon(self::ICONS[$theme])
            ->setLogo(self::LOGO)
            ->setCategory((new Category())->setName('Consumer Electronics'))
        ;

        foreach (['Variant A (Blue)', 'Variant B (Red)'] as $name) {
            $product->addVariant((new Variant())->setName($name));
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'themes' => $this->getThemeUrls($request, $theme),
        ]);
    }

    /**
     * @return array<string, string> theme => URL of the current page in this theme
     */
    private function getThemeUrls(Request $request, string $currentTheme): array
    {
        $path = substr($request->getPathInfo(), \strlen(Kernel::getPathPrefix($currentTheme)));
        $urls = [];

        foreach (Kernel::THEMES as $theme) {
            $urls[$theme] = $request->getBasePath() . Kernel::getPathPrefix($theme) . $path;
        }

        return $urls;
    }
}
