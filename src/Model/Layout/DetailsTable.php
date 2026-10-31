<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Layout;

use Rem42\SyliusUiShow\Model\CompositeElement;

/**
 * Renders its children as a key/value table (label in a <th>, value in a <td>).
 */
class DetailsTable extends CompositeElement
{
    public function getType(): string
    {
        return 'details_table';
    }

    public function setLabelWidth(string $labelWidth): static
    {
        return $this->setOption('label_width', $labelWidth);
    }

    public function getLabelWidth(): string
    {
        $width = $this->getOption('label_width', '230px');

        return \is_string($width) ? $width : '230px';
    }
}
