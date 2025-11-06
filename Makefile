.PHONY: test init-db test-fresh

COMPOSE_TEST_FILES = -f compose.yaml

# Reset the database
init-db:
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:database:drop --force --if-exists
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:database:create
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:schema:update --force
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:fixtures:load --no-interaction
	docker compose $(COMPOSE_TEST_FILES) exec php bin/console doctrine:migrations:migrate


# Reset the test database
init-db-test:
	docker compose $(COMPOSE_TEST_FILES) exec php sh -c "APP_ENV=test DATABASE_URL='mysql://root:root@db:3306/symfony_test' bin/console doctrine:database:drop --force --if-exists"
	docker compose $(COMPOSE_TEST_FILES) exec db sh -c "mysql -uroot -proot -e 'CREATE DATABASE IF NOT EXISTS symfony_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'"
	docker compose $(COMPOSE_TEST_FILES) exec php sh -c "APP_ENV=test DATABASE_URL='mysql://root:root@db:3306/symfony_test' bin/console doctrine:schema:update --force --env=test"
	docker compose $(COMPOSE_TEST_FILES) exec php sh -c "APP_ENV=test DATABASE_URL='mysql://root:root@db:3306/symfony_test' bin/console doctrine:fixtures:load --no-interaction --env=test"

# Run tests
test:
	docker compose $(COMPOSE_TEST_FILES) exec php ./vendor/bin/phpunit

# Reset test db and run tests
test-fresh: init-db-test test
