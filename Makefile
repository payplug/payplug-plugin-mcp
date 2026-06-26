.PHONY: build up down shell comp-install update test-unit test-unit-inte test-unit-units tu-dep stan cs-lint cs-fix debug ci audit security install

DC = docker compose
PHP = $(DC) run --rm php
PHP_DEBUG = XDEBUG_MODE=debug XDEBUG_SESSION=1 $(DC) run --rm php

GIT = git

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
test-unit:
	$(PHP) vendor/bin/phpunit tests

test-unit-inte:
	$(PHP) vendor/bin/phpunit tests --group integration

test-unit-units:
	$(PHP) vendor/bin/phpunit tests --group units

tu-dep:
	$(PHP) vendor/bin/phpunit tests --display-phpunit-deprecations

stan:
	$(PHP) vendor/bin/phpstan analyse

cs-lint:
	$(PHP) vendor/bin/php-cs-fixer fix --diff --dry-run

cs-fix:
	$(PHP) vendor/bin/php-cs-fixer fix

## Debug (Xdebug 3.x step-debug)
debug:
	$(PHP_DEBUG) bash

## CI (runs all checks)
ci: stan cs-lint test-unit

install: build update comp-install

audit:
	$(PHP) composer audit

security: audit
