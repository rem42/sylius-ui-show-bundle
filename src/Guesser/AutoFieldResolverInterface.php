<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Guesser;

use Rem42\SyliusUiShow\Model\CompositeElementInterface;

interface AutoFieldResolverInterface
{
    /**
     * Replaces every AutoField of the tree by the field guessed for the given resource class.
     *
     * @param class-string $resourceClass
     */
    public function resolve(CompositeElementInterface $element, string $resourceClass): void;
}
