ToDoList
========

Projet #8 OpenClassrooms : Améliorez un projet existant (ToDo & Co).

Ce projet est une application de gestion de tâches (To-Do List) développée avec Symfony. L'objectif de cette itération est d'améliorer la qualité du code, d'ajouter des fonctionnalités, de corriger des anomalies et d'implémenter des tests automatisés.

## Prérequis

*   PHP 8.2 ou supérieur
*   Composer
*   Symfony CLI
*   Base de données (MySQL)

## Installation

1.  **Cloner le projet :**

    ```bash
    git clone https://github.com/Raphael-Marchese/Projet8---TodoList
    cd Projet_8
    ```

2.  **Installer les dépendances :**

    ```bash
    composer install
    ```

3.  **Configuration de la base de données :**

    Configurez votre fichier `.env.local` avec vos accès base de données si nécessaire. Par défaut, vérifiez le fichier `.env`.

    ```bash
    # Création de la base de données
    php bin/console doctrine:database:create

    # Exécution des migrations
    php bin/console doctrine:migrations:migrate

    # Chargement des données de test (Fixtures)
    php bin/console doctrine:fixtures:load
    ```

4.  **Lancer le serveur :**

    ```bash
    symfony server:start
    ```

    L'application sera accessible à l'adresse indiquée (généralement `http://127.0.0.1:8000`).

## Tests

Pour lancer les tests automatisés (PHPUnit) :

```bash
php bin/phpunit
```

Un rapport de couverture de code peut être généré (nécessite Xdebug ou PCOV) :

```bash
php bin/phpunit --coverage-html public/test-coverage
```

## Documentation

*   [Documentation de l'authentification](AUTHENTICATION.md) : Détails techniques sur l'implémentation de la sécurité.
*   [Guide de contribution](CONTRIBUTING.md) : Règles de qualité et processus de développement.

## Audit de Qualité et Performance

Des audits sont réalisés via :
*   **Qualité du code :** Qodana
*   **Performance :** Symfony Profiler
