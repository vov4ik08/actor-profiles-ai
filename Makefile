.DEFAULT_GOAL := up

COMPOSE = docker compose
APP = app
UID := $(shell id -u)
GID := $(shell id -g)

.PHONY: help env up build down logs test migrate yarn_install npm_install composer_install key_generate

help:
	@echo "Targets:"
	@echo "  env              Create .env from .env.example if missing"
	@echo "  up               Start containers (build if needed)"
	@echo "  build            Build app image"
	@echo "  down             Stop and remove containers"
	@echo "  logs             Follow logs"
	@echo "  yarn_install     Install frontend deps into node_modules (in node container)"
	@echo "  npm_install      Alias for yarn_install"
	@echo "  composer_install Install PHP deps into vendor/ (in app container)"
	@echo "  key_generate     Generate APP_KEY (php artisan key:generate)"
	@echo "  migrate          Run migrations"
	@echo "  test             Run tests (php artisan test)"

env:
	@set -e; \
	if [ -f .env ]; then \
		echo ".env already exists."; \
		exit 0; \
	fi; \
	if [ ! -f .env.example ]; then \
		echo "ERROR: .env is missing and .env.example was not found."; \
		exit 1; \
	fi; \
	cp .env.example .env; \
	echo "Created .env from .env.example."

up:
	@$(MAKE) env
	$(COMPOSE) up -d --build db $(APP) web
	@$(MAKE) yarn_install
	$(COMPOSE) up -d node
	@$(MAKE) migrate

build:
	$(COMPOSE) build $(APP)

down:
	$(COMPOSE) down

logs:
	$(COMPOSE) logs -f --tail=200

migrate:
	@set -e; \
	started_db=0; \
	if [ -z "$$($(COMPOSE) ps -q db)" ]; then \
		started_db=1; \
		$(COMPOSE) up -d db; \
	fi; \
	$(COMPOSE) run --rm --build $(APP) php artisan migrate; \
	if [ "$$started_db" = "1" ]; then \
		$(COMPOSE) stop db >/dev/null; \
		$(COMPOSE) rm -f db >/dev/null; \
	fi

yarn_install:
	@set -e; \
	echo "Installing node_modules..."; \
	$(COMPOSE) run --rm --no-deps -u "$(UID):$(GID)" \
		-e HOME=/tmp \
		-e YARN_CACHE_FOLDER=/tmp/.yarn-cache \
		node sh -lc ' \
		corepack enable >/dev/null 2>&1 || true; \
		mkdir -p "$$YARN_CACHE_FOLDER"; \
		yarn install --frozen-lockfile \
	'; \
	echo "node_modules ready."

# Backward-compatible alias
npm_install: yarn_install

composer_install:
	$(COMPOSE) run --rm --build $(APP) composer install

key_generate:
	$(COMPOSE) run --rm --no-deps --build $(APP) php artisan key:generate

test:
	@set -e; \
	started_db=0; \
	if [ -z "$$($(COMPOSE) ps -q db)" ]; then \
		started_db=1; \
		$(COMPOSE) up -d db; \
	fi; \
	$(COMPOSE) run --rm --build $(APP) php artisan test; \
	if [ "$$started_db" = "1" ]; then \
		$(COMPOSE) stop db >/dev/null; \
		$(COMPOSE) rm -f db >/dev/null; \
	fi

