# -------------------------------------------
# Konfiguracja
# -------------------------------------------
COMPOSE      ?= docker compose
APP_SERVICE  ?= app
WORKDIR      ?= /var/www/html/laravel
EXEC         = $(COMPOSE) exec -T $(APP_SERVICE) bash -lc

# -------------------------------------------
# Kontenery
# -------------------------------------------
.PHONY: up stop build rebuild down logs sh setup npm-install

up:             ## kontenery + deps
	$(COMPOSE) up -d
	make setup
	make npm-install

stop:           ## pauza
	$(COMPOSE) stop

down:           ## rm kontenery
	$(COMPOSE) down

build:          ## build z cache
	$(COMPOSE) build

rebuild:        ## build bez cache
	$(COMPOSE) build --no-cache

logs:           ## tail
	$(COMPOSE) logs -f

sh:             ## shell w PHP
	$(COMPOSE) exec $(APP_SERVICE) bash

setup:          ## composer install
	$(EXEC) "cd $(WORKDIR) && composer install"

npm-install:    ## npm install
	$(EXEC) "cd $(WORKDIR) && npm install"

# -------------------------------------------
# Backend – PHP
# -------------------------------------------
.PHONY: test analyse lint fix docs

test:     ## PHPUnit
	$(EXEC) "cd $(WORKDIR) && composer test"

analyse:  ## PHPStan
	$(EXEC) "cd $(WORKDIR) && composer analyse"

lint:     ## Pint check
	$(EXEC) "cd $(WORKDIR) && composer lint"

fix:      ## Pint fix
	$(EXEC) "cd $(WORKDIR) && composer fix"

docs:     ## phpDoc
	$(EXEC) "cd $(WORKDIR) && composer docs"

# -------------------------------------------
# Front-end – Node / Vue / Vite
# -------------------------------------------
.PHONY: dev build-front preview js-lint js-fix lint-all

dev:             ## vite dev
	$(EXEC) "cd $(WORKDIR) && npm run dev"

build-front:     ## vite build
	$(EXEC) "cd $(WORKDIR) && npm run build"

preview:         ## vite preview
	$(EXEC) "cd $(WORKDIR) && npm run preview"

js-lint:         ## ESLint
	$(EXEC) "cd $(WORKDIR) && npm run lint"

js-fix:          ## ESLint --fix
	$(EXEC) "cd $(WORKDIR) && npm run lint:fix"

lint-all:        ## Pint + ESLint
	make lint js-lint

# -------------------------------------------
# CI – lokalny pipeline
# -------------------------------------------
.PHONY: ci
ci:              ## format + static analysis + testy
	make lint-all analyse test
	@echo "✅  Backend + Frontend OK"

# -------------------------------------------
# Help
# -------------------------------------------
.PHONY: help
help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}' 