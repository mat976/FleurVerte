# =============================================================================
# FleurVerte - common developer tasks
# Run `make help` to list available targets.
# =============================================================================

PHP        ?= php
COMPOSER   ?= composer
CONSOLE     = $(PHP) bin/console

.DEFAULT_GOAL := help

.PHONY: help install assets dev db-reset test-db test lint up down logs

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

install: ## Install PHP and JS dependencies
	$(COMPOSER) install
	npm ci

assets: ## Build front-end assets (Webpack Encore)
	npm run build

dev: ## Run the app locally (PHP built-in server on :3010)
	./start.sh

db-reset: ## Drop, recreate the dev schema and load fixtures
	$(CONSOLE) doctrine:database:create --if-not-exists
	$(CONSOLE) doctrine:migrations:migrate --no-interaction --allow-no-migration
	$(CONSOLE) doctrine:fixtures:load --no-interaction

test-db: ## (Re)create the test database schema
	mkdir -p data var
	$(CONSOLE) doctrine:schema:drop --env=test --force --full-database 2>/dev/null || true
	$(CONSOLE) doctrine:schema:create --env=test

test: assets test-db ## Build assets, prepare the test DB and run PHPUnit
	$(PHP) bin/phpunit

lint: ## Run Symfony linters (yaml, twig, container) and validate composer
	$(COMPOSER) validate --strict
	$(CONSOLE) lint:yaml config --parse-tags
	$(CONSOLE) lint:twig templates
	$(CONSOLE) lint:container

up: ## Start the full Docker stack (app + PostgreSQL)
	docker compose up -d --build

down: ## Stop the Docker stack
	docker compose down

logs: ## Tail the application container logs
	docker compose logs -f app
