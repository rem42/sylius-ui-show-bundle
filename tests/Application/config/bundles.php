<?php

declare(strict_types=1);

// Bundles enabled for every theme ("all") or for one theme only (see App\Kernel).
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\UX\TwigComponent\TwigComponentBundle::class => ['all' => true],
    Sylius\TwigHooks\SyliusTwigHooksBundle::class => ['all' => true],
    Rem42\SyliusUiShow\Symfony\Rem42SyliusUiShowBundle::class => ['all' => true],

    // rem42/shadcn-admin-ui and the bundles its components rely on
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['shadcn' => true],
    Symfony\UX\StimulusBundle\StimulusBundle::class => ['shadcn' => true],
    Symfony\UX\Icons\UXIconsBundle::class => ['shadcn' => true],
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['shadcn' => true],
    Sylius\TwigExtra\Symfony\SyliusTwigExtraBundle::class => ['shadcn' => true],
    Symfonycasts\TailwindBundle\SymfonycastsTailwindBundle::class => ['shadcn' => true],
    TalesFromADev\Twig\Extra\Tailwind\Bridge\Symfony\Bundle\TalesFromADevTwigExtraTailwindBundle::class => ['shadcn' => true],
    Rem42\ShadcnAdminUi\Rem42ShadcnAdminUiBundle::class => ['shadcn' => true],
];
