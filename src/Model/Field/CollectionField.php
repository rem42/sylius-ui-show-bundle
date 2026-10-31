<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;
use Rem42\SyliusUiShow\Model\FieldInterface;

/**
 * Renders an iterable value as a table: each column is a field resolved against every item.
 */
class CollectionField extends AbstractField
{
    /** @var array<string, FieldInterface> */
    private array $columns = [];

    public function getType(): string
    {
        return 'collection';
    }

    public function addColumn(FieldInterface $column): static
    {
        $this->columns[$column->getName()] = $column;

        return $this;
    }

    public function withColumns(FieldInterface ...$columns): static
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }

        return $this;
    }

    public function removeColumn(string $name): static
    {
        unset($this->columns[$name]);

        return $this;
    }

    /**
     * @return array<string, FieldInterface>
     */
    public function getColumns(): array
    {
        $columns = array_filter($this->columns, static fn (FieldInterface $column): bool => $column->isEnabled());
        uasort($columns, static fn (FieldInterface $a, FieldInterface $b): int => $b->getPriority() <=> $a->getPriority());

        return $columns;
    }

    /**
     * @return array<string, FieldInterface>
     */
    public function getAllColumns(): array
    {
        return $this->columns;
    }

    public function setEmptyMessage(string $message): static
    {
        return $this->setOption('empty_message', $message);
    }

    public function getEmptyMessage(): string
    {
        $message = $this->getOption('empty_message', 'sylius.ui.no_results_to_display');

        return \is_string($message) ? $message : 'sylius.ui.no_results_to_display';
    }
}
