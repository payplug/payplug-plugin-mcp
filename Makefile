.PHONY: build up down shell install update test stan lint fix rector debug

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
install:
	$(PHP) composer install

update:
	$(PHP) composer update

## Quality
test:
	$(PHP) vendor/bin/phpunit

stan:
	$(PHP) vendor/bin/phpstan analyse

lint:
	$(PHP) vendor/bin/php-cs-fixer fix --diff

fix:
	$(PHP) vendor/bin/php-cs-fixer fix

rector:
	$(PHP) vendor/bin/rector process

rector-dry:
	$(PHP) vendor/bin/rector process --dry-run

## Debug (Xdebug step-debug enabled)
debug:
	$(PHP_DEBUG) bash

## CI (runs all checks)
ci: stan lint test
