<?php

declare(strict_types=1);

namespace Tests\Rem42\SyliusUiShow\Functional\Application\Model;

enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
}
