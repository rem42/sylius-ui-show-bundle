<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Accessor;

use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class ValueAccessor implements ValueAccessorInterface
{
    private readonly PropertyAccessorInterface $propertyAccessor;

    public function __construct(?PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
    }

    public function getValue(mixed $resource, string $path): mixed
    {
        if ('.' === $path || '' === $path) {
            return $resource;
        }

        if (!\is_object($resource) && !\is_array($resource)) {
            return null;
        }

        try {
            return $this->propertyAccessor->getValue($resource, $path);
        } catch (UnexpectedTypeException) {
            // An intermediate value of the path is null (e.g. "customer.email" without customer).
            return null;
        }
    }
}
