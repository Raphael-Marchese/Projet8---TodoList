.PHONY: tests reset-test-db

COMPOSE_TEST_FILES = -f compose.yaml -f compose.test.yaml

tests: reset-test-db
	docker compose $(COMPOSE_TEST_FILES) run --rm php ./vendor/bin/phpunit

reset-test-db:
	docker compose $(COMPOSE_TEST_FILES) run --rm php bin/console doctrine:database:drop --force --env=test --if-exists
	docker compose $(COMPOSE_TEST_FILES) run --rm php bin/console doctrine:database:create --env=test
	docker compose $(COMPOSE_TEST_FILES) run --rm php bin/console doctrine:schema:update --force --env=test
	docker compose $(COMPOSE_TEST_FILES) run --rm php bin/console doctrine:fixtures:load --no-interaction --env=test
