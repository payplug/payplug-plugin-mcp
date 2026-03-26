.PHONY: build up down shell install update test stan lint fix debug release

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

rector-dry:
	$(PHP) vendor/bin/rector process --dry-run

## Release (downgrade src/ to PHP 7.2 into payplug-core/)
release:
	rm -rf payplug-core && mkdir payplug-core && cp -r src payplug-core/ && cp -r tests payplug-core/
	$(PHP) vendor/bin/rector process --config rector.php
	$(PHP) php scripts/generate-release-composer.php

## Debug (Xdebug step-debug enabled)
debug:
	$(PHP_DEBUG) bash

## CI (runs all checks)
ci: stan cs-lint test-unit

install: build update comp-install

audit:
	$(PHP) composer audit

security: audit