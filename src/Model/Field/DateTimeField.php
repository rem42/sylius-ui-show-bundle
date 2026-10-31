<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

class DateTimeField extends AbstractField
{
    public const DEFAULT_FORMAT = 'Y-m-d H:i:s';

    public function getType(): string
    {
        return 'datetime';
    }

    public function setFormat(string $format): static
    {
        return $this->setOption('format', $format);
    }

    public function getFormat(): string
    {
        $format = $this->getOption('format', self::DEFAULT_FORMAT);

        return \is_string($format) ? $format : self::DEFAULT_FORMAT;
    }

    public function setTimezone(?string $timezone): static
    {
        return $this->setOption('timezone', $timezone);
    }

    public function getTimezone(): ?string
    {
        $timezone = $this->getOption('timezone');

        return \is_string($timezone) ? $timezone : null;
    }
}
