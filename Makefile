.PHONY: test init-db test-fresh

COMPOSE_TEST_FILES = -f compose.yaml -f compose.override.yaml

init-db:
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:database:drop --force --env=test --if-exists
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:database:create --env=test
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:schema:update --force --env=test
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:fixtures:load --no-interaction --env=test

test:
	docker compose $(COMPOSE_TEST_FILES) exec php ./vendor/bin/phpunit

test-fresh: init-db test
