.PHONY: help up fresh test clear ide-helper optimize

# Default goal
.DEFAULT_GOAL := help

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Start the Laravel development server
	php artisan serve

fresh: ## Drop all tables, re-run migrations and seed database
	php artisan migrate:fresh --seed

test: ## Run PHPUnit tests
	php artisan test

clear: ## Clear all Laravel caches
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear

optimize: ## Optimize the application for production
	composer install --optimize-autoloader --no-dev
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	php artisan event:cache

ide-helper: ## Generate IDE helper files
	php artisan ide-helper:generate
	php artisan ide-helper:models --nowrite
	php artisan ide-helper:meta

install: ## Install all dependencies and setup project
	composer install
	cp .env.example .env
	php artisan key:generate
	npm install
	npm run build
	php artisan migrate:fresh --seed

build: ## Build assets for production
	npm run build

dev: ## Run assets in development mode with watch
	npm run dev

format: ## Format code with Laravel Pint
	./vendor/bin/pint

db-reset: ## Reset database (alias for fresh)
	php artisan migrate:fresh

db-seed: ## Run database seeders only
	php artisan db:seed

tinker: ## Open Laravel tinker REPL
	php artisan tinker

logs: ## Tail Laravel logs
	tail -f storage/logs/laravel.log
