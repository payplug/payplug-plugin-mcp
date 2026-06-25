.PHONY: build up down shell install update test stan lint fix debug release commit-amend commit-push commit-squash

DC = docker compose
PHP = $(DC) run --rm php
# Xdebug 2.x step-debug: passes remote_enable + remote_autostart via XDEBUG_CONFIG
PHP_DEBUG = XDEBUG_CONFIG="remote_enable=1 remote_autostart=1" $(DC) run --rm php

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

## Release — package src/ + tests/ into payplug-core/
release:
	rm -rf payplug-core && mkdir payplug-core && cp -r src payplug-core/ && cp -r tests payplug-core/
	$(PHP) php scripts/generate-release-composer.php

## Debug (Xdebug 2.x step-debug enabled)
debug:
	$(PHP_DEBUG) bash

## CI (runs all checks)
ci: stan cs-lint test-unit

install: build update comp-install

audit:
	$(PHP) composer audit

security: audit

# Git
commit-amend:
	$(GIT) commit -a --amend --no-edit

commit-push:
	$(GIT) push --force-with-lease

commit-squash:
	$(GIT) reset --soft $$(git merge-base origin/develop HEAD)
	$(GIT) commit --edit -m "$${msg:-$$(git log -1 --pretty=%B)}"

commit-amend-push: commit-amend commit-push
