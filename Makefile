.PHONY: build up down shell install update test stan lint fix rector debug release

DC = docker compose
PHP = $(DC) run --rm php
PHP_DEBUG = XDEBUG_MODE=debug $(DC) run --rm php

## Docker
build:
	$(DC) build

up:
	$(DC) up -d

down:
	$(DC) down

shell:
	$(PHP) bash

## Composer
comp-install:
	$(PHP) composer install

update:
	$(PHP) composer update

## Quality
test:
	$(PHP) vendor/bin/phpunit

stan:
	$(PHP) vendor/bin/phpstan analyse

lint:
	$(PHP) vendor/bin/php-cs-fixer fix --diff --dry-run

fix:
	$(PHP) vendor/bin/php-cs-fixer fix

rector-dry:
	$(PHP) vendor/bin/rector process --dry-run --config rector-release.php

## Release (downgrade src/ to PHP 7.2 into release/)
release:
	rm -rf release && cp -r src release && cp -r tests release
	$(PHP) vendor/bin/rector process --config rector-release.php

## Debug (Xdebug step-debug enabled)
debug:
	$(PHP_DEBUG) bash

## CI (runs all checks)
ci: stan lint test

install: build update comp-install

audit:
	$(PHP) composer audit

security: audit