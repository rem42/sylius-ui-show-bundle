<?php

declare(strict_types=1);

/*
 * Importmap of the shadcn theme: php bin/console importmap:install (with APP_THEME=shadcn).
 */
return [
    'admin' => ['path' => './assets/admin.js', 'entrypoint' => true],
    '@hotwired/stimulus' => ['version' => '3.2.2'],
    '@symfony/stimulus-bundle' => ['path' => '../../vendor/symfony/stimulus-bundle/assets/dist/loader.js'],
];
