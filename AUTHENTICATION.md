# Documentation Technique : Authentification

Cette documentation explique le fonctionnement de l'authentification dans l'application ToDoList. Elle est destinée aux développeurs souhaitant comprendre ou modifier le système de sécurité.

## Vue d'ensemble

L'authentification est gérée par le composant **Symfony Security**. Elle repose sur un système de "Firewalls" (pare-feux) et de "Providers" (fournisseurs d'utilisateurs).

## Fichiers Clés

Voici les fichiers principaux impliqués dans l'authentification :

1.  **`config/packages/security.yaml`** : C'est le fichier de configuration central. Il définit :
    *   L'algorithme de hachage des mots de passe (`bcrypt`).
    *   Le fournisseur d'utilisateurs (`doctrine` via l'entité `User`).
    *   Les règles de pare-feu (`firewalls`) pour sécuriser les URL.
    *   Le contrôle d'accès (`access_control`) pour définir les rôles requis par URL.

2.  **`src/Entity/User.php`** :
    *   Cette classe représente l'utilisateur en base de données.
    *   Elle implémente `UserInterface` et `PasswordAuthenticatedUserInterface`, ce qui permet à Symfony de l'utiliser pour l'authentification.
    *   Elle contient les propriétés `username`, `password`, `email` et `roles`.

3.  **`src/Controller/SecurityController.php`** :
    *   Gère la route `/login` pour afficher le formulaire de connexion.
    *   Gère la déconnexion (bien que la logique soit interceptée par le firewall).

## Fonctionnement de l'Authentification

### 1. Stockage des Utilisateurs

Les utilisateurs sont stockés dans la base de données relationnelle via l'ORM Doctrine. La table correspondante est `user`.

*   **Identifiant** : Le champ `username` est utilisé comme identifiant unique pour la connexion.
*   **Mot de passe** : Le mot de passe est stocké sous forme hachée. L'algorithme configuré est **bcrypt**.

### 2. Processus de Connexion

Le firewall `main` est configuré pour utiliser un `form_login` (formulaire de connexion). Voici le déroulé :

1.  L'utilisateur accède à `/login`.
2.  Il saisit son `username` et son `password`.
3.  Symfony intercepte la soumission sur la route `login_check` (configurée dans `security.yaml`).
4.  Le système récupère l'utilisateur via le `UserProvider` (Doctrine).
5.  Il vérifie si le mot de passe saisi correspond au hash stocké.
6.  **Succès** : Une session est créée, l'utilisateur est redirigé (par défaut vers la page d'accueil `/`).
7.  **Échec** : L'utilisateur est redirigé vers le formulaire avec une erreur.

### 3. Gestion des Rôles

Les rôles définissent les permissions des utilisateurs :

*   **`ROLE_USER`** : Rôle par défaut. Permet de créer et gérer ses tâches.
*   **`ROLE_ADMIN`** : Rôle administrateur. Permet de gérer les utilisateurs (création, modification).

La hiérarchie des rôles est définie dans `security.yaml` :

```yaml
role_hierarchy:
    ROLE_ADMIN: [ROLE_USER]
```

Cela signifie qu'un administrateur possède implicitement tous les droits d'un utilisateur standard.

## Sécurité des Mots de Passe

Lors de la création ou de la modification d'un utilisateur, le mot de passe doit **toujours** être haché avant d'être persisté en base. Cela est géré dans le contrôleur (`UserController`) ou via un écouteur d'événements Doctrine, en utilisant le service `UserPasswordHasherInterface`.

## Comment modifier l'authentification ?

*   **Modifier le formulaire de login** : Modifiez le template `templates/security/login.html.twig`.
*   **Ajouter des champs à l'utilisateur** : Modifiez l'entité `src/Entity/User.php` et créez une migration Doctrine.
*   **Changer les règles d'accès** : Modifiez la section `access_control` dans `config/packages/security.yaml`.



