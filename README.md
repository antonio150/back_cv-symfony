# CV Backend

Application backend Symfony 7.4 dédiée à la gestion des données d’un CV, avec une API REST exposée via API Platform, une authentification JWT et la génération de PDF.

## 1. Présentation

Cette application sert de moteur principal pour:

- gérer les informations d’un utilisateur et de son CV,
- exposer une API REST sécurisée,
- gérer l’authentification via email/mot de passe ou Google OAuth,
- générer un PDF de CV à partir de templates Twig,
- stocker les documents/images associés au profil.

Le projet est basé sur Symfony et utilise plusieurs composants clés :

- Symfony Framework
- API Platform
- Doctrine ORM
- PostgreSQL
- Lexik JWT Authentication
- Google OAuth2
- Dompdf pour la génération PDF
- Twig pour les templates HTML/PDF

## 2. Structure du projet

```text
.
├── config/                 # Configuration Symfony
├── migrations/             # Migrations Doctrine
├── public/                 # Point d’entrée web et fichiers publics
├── src/                    # Code source PHP
│   ├── ApiResource/        # Ressources API Platform
│   ├── Controller/         # Contrôleurs personnalisés
│   ├── Entity/             # Entités Doctrine
│   └── Security/           # Sécurité / logique spécifique
├── templates/              # Templates Twig
├── tests/                  # Tests PHPUnit
├── .env                    # Variables d’environnement
├── compose.yaml            # Services Docker
├── compose.override.yaml   # Configuration locale Docker
├── composer.json           # Dépendances PHP
├── importmap.php           # Importmap Symfony
├── phpunit.dist.xml        # Configuration PHPUnit
├── bin/                    # Scripts Symfony et PHPUnit
└── README.md               # Documentation du projet
```

## 3. Fonctionnement général

### Architecture applicative

L’application suit le modèle Symfony classique :

- les entités Doctrine représentent les données du CV,
- les ressources API Platform exposent ces données via REST/JSON,
- les contrôleurs personnalisés gèrent des cas métiers spécifiques comme la connexion Google,
- la sécurité est assurée par Symfony Security + JWT,
- les templates Twig servent à produire les vues HTML/PDF de génération de CV.

### Authentification

Le projet configure une sécurité stateless pour l’API :

- `/api/login` : connexion classique via email/mot de passe,
- `/api/auth/google` : connexion via token Google,
- les autres endpoints `/api/*` sont protégés par JWT.

### stockage et fichiers

Les images et autres fichiers utilisateur sont gérés via des uploads dans le dossier `public/uploads` et/ou les chemins stockés dans les entités.

### Génération PDF

Le template `templates/contenue_cv/cv_pdf.html.twig` est utilisé pour produire un document PDF à partir des données du CV. Cela permet d’imprimer ou de télécharger une version standardisée du CV.

## 4. Prérequis

Avant de lancer le projet, il faut avoir installé :

- PHP 8.2+
- Composer
- Symfony CLI (recommandé)
- Docker + Docker Compose
- PostgreSQL (ou le conteneur fourni par Docker)

## 5. Installation

### 1) Cloner le projet

```bash
git clone <url-du-repo>
cd back_cv
```

### 2) Installer les dépendances PHP

```bash
composer install
```

### 3) Configurer les variables d’environnement

Vérifier le fichier `.env` et ajuster si nécessaire :

- base de données PostgreSQL,
- clés JWT,
- identifiants Google OAuth,
- autres variables système.

Exemple de configuration de base :

```env
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

### 4) Démarrer la base de données

```bash
docker compose up -d database
```

Si vous utilisez aussi Mailpit pour les e-mails :

```bash
docker compose up -d mailer
```

## 6. Commandes de démarrage

### Lancer le serveur Symfony

```bash
symfony serve
```

Ou bien en mode PHP interne :

```bash
php -S 127.0.0.1:8000 public/index.php
```

### Lancer les services Docker

```bash
docker compose up -d
```

### Vérifier l’état des conteneurs

```bash
docker compose ps
```

## 7. Commandes utiles

### Base de données

Créer la base de données :

```bash
php bin/console doctrine:database:create
```

Exécuter les migrations :

```bash
php bin/console doctrine:migrations:migrate
```

Vérifier le statut des migrations :

```bash
php bin/console doctrine:migrations:status
```

### Cache et maintenance

Vider le cache Symfony :

```bash
php bin/console cache:clear
```

### Tests

Lancer les tests :

```bash
php bin/phpunit
```

Ou en mode spécifique :

```bash
php bin/phpunit --filter NomDuTest
```

### Génération de schéma/contrôles

Créer un contrôleur :

```bash
php bin/console make:controller
```

Créer une entité :

```bash
php bin/console make:entity
```

## 8. Exemple de flux utilisateur

### 1. Inscription / connexion

- L’utilisateur envoie ses identifiants sur `/api/login`.
- Symfony Security vérifie les données via le provider `Utilisateur`.
- Lexik JWT retourne un token.
- Le token est ensuite utilisé pour accéder aux endpoints protégés.

### 2. Authentification Google

- Le front envoie un token Google à `/api/auth/google`.
- Le backend vérifie le token avec Google Client.
- Si l’utilisateur n’existe pas, il est créé automatiquement.
- Un JWT est renvoyé au client.

### 3. Accès au CV

- L’utilisateur récupère ses ressources via l’API Platform.
- La donnée est sérialisée en JSON.
- Les templates Twig peuvent ensuite générer une version PDF du CV.

## 9. Points importants

- Le projet utilise un système d’authentification stateless avec JWT.
- Toutes les routes `/api` sont protégées par défaut.
- Les fichiers publics sont servis via `public/`.
- La base de données est gérée via Doctrine et les migrations.
- Les composants API Platform et Symfony sont centralisés dans `config/`.

## 10. Dépannage rapide

### Problème de base de données

```bash
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Problème de cache

```bash
php bin/console cache:clear
php bin/console cache:warmup
```

### Problème de dépendances

```bash
composer install
composer dump-autoload
```

## 11. Commandes à retenir

```bash
composer install
composer update
php bin/console doctrine:migrations:migrate
php bin/console cache:clear
php bin/phpunit
symfony serve
```

## 12. Conclusion

Ce backend Symfony sert de base solide pour la gestion d’un portfolio ou d’un CV numérique, avec une API sécurisée, une gestion des données structurée et un système de génération PDF prêt à l’emploi.

Pour toute modification de la structure des données, il est recommandé de passer par les entités Doctrine puis de générer les migrations associées.
