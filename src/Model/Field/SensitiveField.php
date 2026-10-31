<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

use Rem42\SyliusUiShow\Model\AbstractField;

/**
 * Hides a sensitive value: blurred until hovered, or masked with a character.
 */
class SensitiveField extends AbstractField
{
    public const MODE_BLUR = 'blur';

    public const MODE_MASK = 'mask';

    public function getType(): string
    {
        return 'sensitive';
    }

    public function blur(string $radius = '5px'): static
    {
        return $this->addOptions(['mode' => self::MODE_BLUR, 'blur_radius' => $radius]);
    }

    /**
     * @param int $visible number of trailing characters left visible
     */
    public function mask(string $char = '*', int $visible = 0): static
    {
        return $this->addOptions(['mode' => self::MODE_MASK, 'char' => $char, 'visible' => $visible]);
    }

    public function getMode(): string
    {
        $mode = $this->getOption('mode');

        return \is_string($mode) ? $mode : self::MODE_BLUR;
    }

    public function getBlurRadius(): string
    {
        $radius = $this->getOption('blur_radius');

        return \is_string($radius) ? $radius : '5px';
    }

    public function getChar(): string
    {
        $char = $this->getOption('char');

        return \is_string($char) ? $char : '*';
    }

    public function getVisible(): int
    {
        $visible = $this->getOption('visible');

        return \is_int($visible) ? max(0, $visible) : 0;
    }

    protected static function getDefaultOptions(): array
    {
        return ['mode' => self::MODE_BLUR, 'blur_radius' => '5px', 'char' => '*', 'visible' => 0];
    }
}
