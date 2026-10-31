<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

class StringField extends AbstractField
{
    public function getType(): string
    {
        return 'string';
    }

    /**
     * Converts new lines to <br> tags.
     */
    public function setMultiline(bool $multiline = true): static
    {
        return $this->setOption('multiline', $multiline);
    }

    public function isMultiline(): bool
    {
        return true === $this->getOption('multiline', false);
    }

    /**
     * Truncates the value to the given length (null to disable).
     */
    public function setMaxLength(?int $maxLength): static
    {
        return $this->setOption('max_length', $maxLength);
    }

    public function getMaxLength(): ?int
    {
        $maxLength = $this->getOption('max_length');

        return \is_int($maxLength) ? $maxLength : null;
    }
}
