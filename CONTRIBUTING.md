# Guide de Contribution

Bienvenue sur le projet ToDoList ! Ce document détaille les processus et les règles à suivre pour contribuer au développement de l'application.

## Processus de Développement

Nous utilisons un flux de travail basé sur Git.

1.  **Fork & Clone** : Clonez le dépôt sur votre machine locale.
2.  **Branche** : Créez une nouvelle branche pour chaque fonctionnalité ou correction de bug.
    *   Format : `feature/nom-de-la-feature` ou `fix/nom-du-bug`.
3.  **Développement** : Effectuez vos modifications en respectant les standards de qualité.
4.  **Tests** : Assurez-vous que tous les tests passent et écrivez de nouveaux tests pour votre code.
5.  **Pull Request** : Soumettez une Pull Request (PR) vers la branche principale. Décrivez clairement vos changements.

## Standards de Qualité

Pour maintenir une base de code saine et maintenable, les règles suivantes doivent être respectées :

### 1. Qualité du Code

*   **Standards PHP** : Le code doit respecter les standards **PSR-12**.
*   **Analyse Statique** : Le code doit être analysé pour détecter les problèmes potentiels. Nous utilisons des outils comme Codacy, CodeClimate ou dernièrement, Qodana. Assurez-vous de ne pas introduire de nouvelles "code smells".
*   **Clarté** : Nommez vos variables, classes et méthodes de manière explicite (en anglais de préférence, ou cohérent avec l'existant).

### 2. Tests Automatisés

La qualité de l'application repose sur une suite de tests robuste.

*   **Framework** : PHPUnit est utilisé pour les tests unitaires et fonctionnels.
*   **Couverture** : Tout nouveau code doit être couvert par des tests.
    *   **Objectif** : Maintenir un taux de couverture de code supérieur à **70%**.
*   **Exécution** : Lancez les tests avant chaque commit :
    ```bash
    php bin/phpunit
    ```

### 3. Performance

*   Évitez les requêtes N+1 avec Doctrine.
*   Utilisez le **Symfony Profiler** pour vérifier l'impact de vos modifications sur les performances (temps de réponse, consommation mémoire, nombre de requêtes SQL).

## Règles Spécifiques au Projet

### Gestion des Tâches et Utilisateurs

*   **Attribution** : Une tâche doit toujours être liée à un utilisateur.
*   **Rôles** :
    *   `ROLE_ADMIN` : Accès à la gestion des utilisateurs.
    *   `ROLE_USER` : Accès à la gestion des tâches.
*   **Suppression** :
    *   Un utilisateur ne peut supprimer que ses propres tâches.
    *   Les tâches "anonymes" ne peuvent être supprimées que par un administrateur.

## Rapport d'Audit

Si vous effectuez une refonte ou une optimisation importante, il est recommandé de produire un audit "Avant/Après" (Qualité et Performance) pour valider les gains obtenus.

