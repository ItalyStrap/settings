PROJECT_NAME := italystrap-settings
COMPOSE := docker compose -f .docker/docker-compose.yml --env-file .docker/test.env

# The container user writes in the package folder, map it to the host user
export HOST_UID ?= $(shell id -u)
export HOST_GID ?= $(shell id -g)
CONTAINER := $(PROJECT_NAME)_test
PACKAGE_DIR := /var/www/html/wp-content/plugins/settings
EXEC := docker exec --user www-data -w $(PACKAGE_DIR) $(CONTAINER)
EXEC_TTY := docker exec -it --user www-data -w $(PACKAGE_DIR) $(CONTAINER)

# Extra arguments, e.g. make composer ARGS="show -D" or make unit ARGS="--debug"
ARGS ?=

.DEFAULT_GOAL := help

.PHONY: help
help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-14s\033[0m %s\n", $$1, $$2}'

.PHONY: up
up: ## Start the containers
	$(COMPOSE) up -d --wait

.PHONY: down
down: ## Stop and remove the containers
	$(COMPOSE) down --volumes

.PHONY: build
build: ## Rebuild the image of the core
	$(COMPOSE) build wordpress

.PHONY: shell
shell: ## Open a shell in the container of the core
	$(EXEC_TTY) bash

.PHONY: composer
composer: ## Run Composer in the container, pass the command with ARGS
	$(EXEC) composer $(ARGS)

.PHONY: install
install: ## Install the Composer dependencies and build the Codeception actors
	$(EXEC) composer install
	$(EXEC) vendor/bin/codecept build

.PHONY: cs
cs: ## Run PHP CodeSniffer
	$(EXEC) composer cs

.PHONY: cs-fix
cs-fix: ## Fix the coding standard with PHP Code Beautifier
	$(EXEC) composer cs:fix

.PHONY: stan
stan: ## Run PHPStan
	$(EXEC) composer stan

.PHONY: unit
unit: ## Run the unit suite
	$(EXEC) vendor/bin/codecept run unit $(ARGS)

.PHONY: integration
integration: ## Run the integration suite, the core is loaded
	$(EXEC) vendor/bin/codecept run integration $(ARGS)

.PHONY: tests
tests: unit integration ## Run all the suites

.PHONY: infection
infection: ## Run the mutation tests on the unit suite
	$(EXEC) composer infection

.PHONY: rector
rector: ## Show the changes Rector would apply
	$(EXEC) composer rector

.PHONY: rector-fix
rector-fix: ## Apply the Rector changes
	$(EXEC) composer rector:fix

.PHONY: qa
qa: cs stan rector tests infection ## Run all the checks

.PHONY: clean
clean: ## Remove the Codeception output
	$(EXEC) vendor/bin/codecept clean
