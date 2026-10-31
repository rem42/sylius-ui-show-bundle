<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Guesser;

use Rem42\SyliusUiShow\Model\AbstractField;
use Rem42\SyliusUiShow\Model\CompositeElementInterface;
use Rem42\SyliusUiShow\Model\Field\AutoField;
use Rem42\SyliusUiShow\Model\Field\StringField;

final class AutoFieldResolver implements AutoFieldResolverInterface
{
    public function __construct(
        private readonly FieldGuesserInterface $guesser,
    ) {}

    public function resolve(CompositeElementInterface $element, string $resourceClass): void
    {
        foreach ($element->getAllChildren() as $name => $child) {
            if ($child instanceof AutoField) {
                $element->replaceChild($name, $this->createField($child, $resourceClass));

                continue;
            }

            if ($child instanceof CompositeElementInterface) {
                $this->resolve($child, $resourceClass);
            }
        }
    }

    /**
     * @param class-string $resourceClass
     */
    private function createField(AutoField $autoField, string $resourceClass): AbstractField
    {
        $path = $autoField->getPath();

        // Nested paths ("customer.email") cannot be guessed from the resource class.
        $guess = str_contains($path, '.') ? null : $this->guesser->guess($resourceClass, $path);

        $field = null !== $guess ? $guess->createField($autoField->getName()) : StringField::create($autoField->getName());

        return $field->mergeStateFrom($autoField);
    }
}
