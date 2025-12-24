# Documentation Technique : Authentification

## Sécurité des Mots de Passe

Lors de la création ou de la modification d'un utilisateur, le mot de passe doit **toujours** être haché avant d'être persisté en base. Cela est géré dans le contrôleur (`UserController`) ou via un écouteur d'événements Doctrine, en utilisant le service `UserPasswordHasherInterface`.
## Comment modifier l'authentification ?

*   **Modifier le formulaire de login** : Modifiez le template `templates/security/login.html.twig`.
*   **Ajouter des champs à l'utilisateur** : Modifiez l'entité `src/Entity/User.php` et créez une migration Doctrine.
*   **Changer les règles d'accès** : Modifiez la section `access_control` dans `config/packages/security.yaml`.
Cela signifie qu'un administrateur possède implicitement tous les droits d'un utilisateur standard.
```
    ROLE_ADMIN: [ROLE_USER]
role_hierarchy:
```yaml
La hiérarchie des rôles est définie dans `security.yaml` :

*   **`ROLE_ADMIN`** : Rôle administrateur. Permet de gérer les utilisateurs (création, modification).
*   **`ROLE_USER`** : Rôle par défaut. Permet de créer et gérer ses tâches.

Les rôles définissent les permissions des utilisateurs :

### 3. Gestion des Rôles

7.  **Échec** : L'utilisateur est redirigé vers le formulaire avec une erreur.
6.  **Succès** : Une session est créée, l'utilisateur est redirigé (par défaut vers la page d'accueil `/`).
5.  Il vérifie si le mot de passe saisi correspond au hash stocké.
4.  Le système récupère l'utilisateur via le `UserProvider` (Doctrine).
3.  Symfony intercepte la soumission sur la route `login_check` (configurée dans `security.yaml`).
2.  Il saisit son `username` et son `password`.
1.  L'utilisateur accède à `/login`.

Le firewall `main` est configuré pour utiliser un `form_login` (formulaire de connexion).

### 2. Processus de Connexion

*   **Mot de passe** : Le mot de passe est stocké sous forme hachée. L'algorithme configuré est **bcrypt**.
*   **Identifiant** : Le champ `username` est utilisé comme identifiant unique pour la connexion.

La table correspondante est `user`.
Les utilisateurs sont stockés dans la base de données relationnelle via l'ORM Doctrine.

### 1. Stockage des Utilisateurs

## Fonctionnement de l'Authentification

    *   Gère la déconnexion (bien que la logique soit interceptée par le firewall).
    *   Gère la route `/login` pour afficher le formulaire de connexion.
3.  **`src/Controller/SecurityController.php`** :

    *   Elle contient les propriétés `username`, `password`, `email` et `roles`.
    *   Elle implémente `UserInterface` et `PasswordAuthenticatedUserInterface`, ce qui permet à Symfony de l'utiliser pour l'authentification.
    *   Cette classe représente l'utilisateur en base de données.
2.  **`src/Entity/User.php`** :

    *   Le contrôle d'accès (`access_control`) pour définir les rôles requis par URL.
    *   Les règles de pare-feu (`firewalls`) pour sécuriser les URL.
    *   Le fournisseur d'utilisateurs (`doctrine` via l'entité `User`).
    *   L'algorithme de hachage des mots de passe (`bcrypt`).
1.  **`config/packages/security.yaml`** : C'est le fichier de configuration central. Il définit :

Voici les fichiers principaux impliqués dans l'authentification :

## Fichiers Clés

L'authentification est gérée par le composant **Symfony Security**. Elle repose sur un système de "Firewalls" (pare-feux) et de "Providers" (fournisseurs d'utilisateurs).

## Vue d'ensemble

Cette documentation explique le fonctionnement de l'authentification dans l'application ToDoList. Elle est destinée aux développeurs souhaitant comprendre ou modifier le système de sécurité.


