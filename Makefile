.PHONY: tests reset-test-db

# Variable pour les fichiers de configuration de test
COMPOSE_TEST_FILES = -f compose.yaml -f compose.test.yaml

# Commande pour lancer les tests
# On passe la variable COMPOSE_TEST_FILES à chaque commande
tests: reset-test-db
	docker compose $(COMPOSE_TEST_FILES) exec php ./vendor/bin/phpunit

# Commande pour réinitialiser la BDD de test
reset-test-db:
	docker compose $(COMPOSE_TEST_FILES) exec php php bin/console doctrine:database:drop --force --env=test --if-exists
	docker compose $(COMPOSE_TEST_FILES) exec php php bin/console doctrine:database:create --env=test
	docker compose $(COMPOSE_TEST_FILES) exec php php bin/console doctrine:schema:update --force --env=test
	docker compose $(COMPOSE_TEST_FILES) exec php php bin/console doctrine:fixtures:load --no-interaction --env=test
