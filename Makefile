# Makefile description: https://www.alexedwards.net/blog/a-time-saving-makefile-for-your-go-projects
PROJECT = "mtt"
APP_CONTAINER = "mtt-app"
RABBITMQ_CONTAINER = "mtt-rabbitmq"
DOCKER_COMPOSE_FILE = "./deploy/local/docker-compose.yaml"

TEST_PROJECT = "mtt-test"
TEST_PROJECT_RUN = "mtt-test-run"
TEST_APP_CONTAINER = "mtt-app-test"
TEST_RUN_APP_CONTAINER = "run-mtt-app-test"
TEST_DOCKER_COMPOSE_FILE = "./deploy/test/docker-compose.test.yaml"

# ==================================================================================== #
# HELPERS
# ==================================================================================== #

## help: print this help message
.PHONY: help
help:
	@echo 'Usage:'
	@sed -n 's/^##//p' ${MAKEFILE_LIST} | column -t -s ':' |  sed -e 's/^/ /'

.PHONY: confirm
confirm:
	@echo -n 'Are you sure? [y/N] ' && read ans && [ $${ans:-N} = y ]

.PHONY: no-dirty
no-dirty:
	git diff --exit-code


# ==================================================================================== #
# QUALITY CONTROL
# ==================================================================================== #
## test: run all tests before code pushing to repository
.PHONY: test
test: up-test run-test


## pint: run Laravel Pint (code style fixer)
.PHONY: pint
pint:
	docker compose -p $(PROJECT) -f $(DOCKER_COMPOSE_FILE) exec $(APP_CONTAINER) ./vendor/bin/pint

## phpstan: run PhpStan (static code analyzer)
.PHONY: phpstan
phpstan:
	./vendor/bin/phpstan analyse --memory-limit=2G
# ==================================================================================== #
# DEVELOPMENT
# ==================================================================================== #



# ==================================================================================== #
# INSIDE DOCKER CONTAINER
# ==================================================================================== #

## vim: install vim to in the container
.PHONY: vim
vim:
	apt-get update && apt-get install -y vim

# ==================================================================================== #
# LOCAL RUN
# ==================================================================================== #
## up: docker compose up -d
.PHONY: up
up:
	docker compose -p $(PROJECT) -f $(DOCKER_COMPOSE_FILE) up -d --build
	echo "http://localhost:80"

## bash: [LOC] Connect to app container
.PHONY: bash
bash:
	docker compose -p $(PROJECT) -f $(DOCKER_COMPOSE_FILE) exec $(APP_CONTAINER) bash

## up-test: docker compose up -d WITH Test environment
.PHONY: up-test
up-test:
	docker compose -p $(TEST_PROJECT) -f $(TEST_DOCKER_COMPOSE_FILE) up -d --build

## run-test: docker compose up -d WITH Test environment
.PHONY: run-test
run-test:
	docker compose -p $(TEST_PROJECT) -f $(TEST_DOCKER_COMPOSE_FILE) exec $(TEST_APP_CONTAINER) php artisan test

## bash-test: [LOC] Connect to test app container
.PHONY: bash-test
bash-test:
	docker compose -p $(TEST_PROJECT) -f $(TEST_DOCKER_COMPOSE_FILE) exec $(TEST_APP_CONTAINER) bash
