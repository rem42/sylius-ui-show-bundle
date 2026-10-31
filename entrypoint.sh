#!/bin/sh
set -e

# Dependencies (first install: the project is mounted as a volume, so vendor/ comes from the host)
if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

# Assets of the shadcn theme of the demo: importmap packages and Tailwind CSS build
APP_THEME=shadcn php tests/Application/bin/console importmap:install
APP_THEME=shadcn php tests/Application/bin/console tailwind:build

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"
