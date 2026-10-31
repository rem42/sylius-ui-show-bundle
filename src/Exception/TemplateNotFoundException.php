<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Exception;

use Rem42\SyliusUiShow\Model\ElementInterface;

final class TemplateNotFoundException extends \RuntimeException implements ExceptionInterface
{
    public static function forElement(ElementInterface $element): self
    {
        return new self(sprintf(
            'No template configured for element "%s" of type "%s". Call setTemplate() on the element or configure "rem42_sylius_ui_show.templates.%s".',
            $element->getName(),
            $element->getType(),
            $element->getType(),
        ));
    }

    public static function forType(string $type): self
    {
        return new self(sprintf('No template configured for type "%s". Configure "rem42_sylius_ui_show.templates.%s".', $type, $type));
    }
}
