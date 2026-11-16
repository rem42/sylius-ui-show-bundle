.PHONY: up assets down bash console install test phpstan cs cs-fix twig-cs twig-fix ci

DC = docker compose
PHP = $(DC) exec php

## Demo application (tests/Application) on http://localhost:8080/ (bootstrap)
## and http://localhost:8080/shadcn (shadcn)
up:
	$(DC) up -d --build
	@echo "\n  ➜ http://localhost:8080/ \n"
	@echo "\n  ➜ http://localhost:8080/shadcn \n"

assets:
	$(PHP) APP_THEME=shadcn php tests/Application/bin/console importmap:install
	$(PHP) APP_THEME=shadcn php tests/Application/bin/console tailwind:build

down:
	$(DC) down

bash:
	$(PHP) bash

console:
	$(PHP) php tests/Application/bin/console $(c)

## Development
install:
	$(PHP) composer install

test:
	$(PHP) vendor/bin/phpunit

phpstan:
	$(PHP) vendor/bin/phpstan analyse --memory-limit=-1

cs:
	$(PHP) vendor/bin/php-cs-fixer check --diff

cs-fix:
	$(PHP) vendor/bin/php-cs-fixer fix

twig-cs:
	$(PHP) vendor/bin/twig-cs-fixer lint

twig-fix:
	$(PHP) vendor/bin/twig-cs-fixer lint --fix

## Everything the CI checks
ci: cs twig-cs phpstan test
