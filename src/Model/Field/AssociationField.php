<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;
use Rem42\SyliusUiShow\Model\Color;

class AssociationField extends AbstractField
{
    public const MODE_LINK = 'link';

    public const MODE_LIST = 'list';

    public const MODE_INLINE = 'inline';

    public const MODE_BADGE = 'badge';

    public function getType(): string
    {
        return 'association';
    }

    public function setDisplayMode(string $displayMode): static
    {
        return $this->setOption('display_mode', $displayMode);
    }

    public function getDisplayMode(): string
    {
        $mode = $this->getOption('display_mode', self::MODE_LINK);

        return \is_string($mode) ? $mode : self::MODE_LINK;
    }

    /**
     * Property path of the associated object used as label (defaults to its string representation).
     */
    public function setLabelProperty(?string $labelProperty): static
    {
        return $this->setOption('label_property', $labelProperty);
    }

    public function getLabelProperty(): ?string
    {
        $property = $this->getOption('label_property');

        return \is_string($property) ? $property : null;
    }

    /**
     * @param array<string, string> $parameters route parameter => property path of the associated object
     */
    public function setRoute(?string $route, array $parameters = ['id' => 'id']): static
    {
        $this->setOption('route', $route);

        return $this->setOption('route_parameters', $parameters);
    }

    public function getRoute(): ?string
    {
        $route = $this->getOption('route');

        return \is_string($route) ? $route : null;
    }

    /**
     * @return array<string, string>
     */
    public function getRouteParameters(): array
    {
        if (!$this->hasOption('route_parameters')) {
            return ['id' => 'id'];
        }

        $parameters = [];

        foreach ($this->getStringMapOption('route_parameters') as $parameter => $path) {
            $parameters[(string) $parameter] = $path;
        }

        return $parameters;
    }

    public function setBadgeColor(Color|string $color): static
    {
        return $this->setOption('badge_color', $color);
    }

    public function getBadgeColor(): string
    {
        return $this->getColorOption('badge_color', Color::Primary) ?? Color::Primary->value;
    }
}
