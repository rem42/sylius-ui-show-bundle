<?php

declare(strict_types=1);

namespace Rem42\SyliusUiShow\Model\Field;

class FileField extends UrlField
{
    public function getType(): string
    {
        return 'file';
    }
}
