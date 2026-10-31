<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Twig\Extension;

use Rem42\SyliusUiShow\Twig\Runtime\ShowRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class ShowExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('rem42_sylius_ui_show', [ShowRuntime::class, 'renderShow'], ['is_safe' => ['html']]),
            new TwigFunction('rem42_sylius_ui_show_exists', [ShowRuntime::class, 'hasShow']),
            new TwigFunction('rem42_sylius_ui_show_render', [ShowRuntime::class, 'renderElement'], ['is_safe' => ['html']]),
            new TwigFunction('rem42_sylius_ui_show_value', [ShowRuntime::class, 'getValue']),
            new TwigFunction('rem42_sylius_ui_show_label', [ShowRuntime::class, 'getLabel']),
            new TwigFunction('rem42_sylius_ui_show_attributes', [ShowRuntime::class, 'renderAttributes'], ['is_safe' => ['html']]),
            new TwigFunction('rem42_sylius_ui_show_template', [ShowRuntime::class, 'getTemplate']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('rem42_sylius_ui_show_string', [ShowRuntime::class, 'stringify']),
        ];
    }
}
