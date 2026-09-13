# Fil Rouge

## Sommaire

- [Présentation du projet](#présentation-du-projet)
- [Objectifs](#objectifs)
- [Description graphique et ergonomique](#description-graphique-et-ergonomique)
- [Fonctionnalités](#fonctionnalités)
- [Choix technologiques](#choix-technologiques)
- [Compatibilité](#compatibilité)
- [Sécurité et conformité](#sécurité-et-conformité)
- [Guide d'installation et d'exécution](#guide-dinstallation-et-dexécution)
- [Conclusion](#conclusion)

## Présentation du projet

### Présentation de l'entreprise

Impact est une plateforme web inspirée de Pinterest, permettant de partager et de découvrir des contenus multimédias tels que des photos et des vidéos. Le projet est développé dans le cadre d'un exercice académique ou d'un portfolio personnel.

### Nom du site

**Impact**

### Public cible

- Passionnés de photographie et de vidéographie souhaitant partager leurs œuvres.
- Curieux et amateurs de contenu visuel à la recherche d'inspiration.
- Développeurs et designers intéressés par les projets open source.

### Technologies utilisées

- **Frontend :** HTML5, CSS3, Tailwind CSS, Blade (Laravel) et JavaScript.
- **Backend :** PHP avec le framework Laravel.
- **Base de données :** MySQL.

### Périmètre du projet

- Publication, consultation et interaction avec des contenus multimédias.
- Design responsive adapté aux ordinateurs, tablettes et smartphones.
- Interface conviviale favorisant une navigation intuitive.

## Objectifs

- Offrir une plateforme intuitive pour le partage de photos et de vidéos.
- Permettre aux utilisateurs de publier, consulter, commenter et aimer des contenus.
- Assurer une gestion efficace des utilisateurs et des contenus via une interface d'administration.
- Garantir la sécurité des données et des interactions sur la plateforme.

## Description graphique et ergonomique

### Charte graphique

- Design épuré et moderne mettant en valeur le contenu visuel.
- Palette de couleurs neutres avec des accents vifs pour les éléments interactifs.
- Typographie claire et lisible pour une meilleure expérience utilisateur.

### Inspirations

- **Pinterest :** disposition en grille des contenus.
- **Unsplash :** mise en avant d'images de haute qualité.
- **Instagram :** interactions sociales telles que les likes et les commentaires.

### Logo

- Icône représentant une épingle ou un appareil photo, symbolisant le partage de contenu visuel.
- Typographie moderne reflétant la simplicité et l'élégance de la plateforme.

## Fonctionnalités

### Structure du site

- **Accueil :** présentation des contenus récents et populaires.
- **Galerie :** affichage en grille des photos et vidéos.
- **Publication :** formulaire pour publier de nouveaux contenus.
- **Profil utilisateur :** informations et contenus de l'utilisateur.
- **Connexion / inscription :** accès au compte utilisateur.
- **Administration :** gestion des utilisateurs et des contenus.

### Fonctionnalités par rôle

#### Utilisateurs

- **Inscription et connexion :** création de compte et authentification sécurisée.
- **Publication de contenus :** upload de photos ou vidéos avec titre et description.
- **Consultation de la galerie :** navigation parmi les contenus publiés.
- **Interactions sociales :** possibilité d'aimer et de commenter les contenus.
- **Gestion du profil :** modification des informations personnelles et visualisation des contenus publiés.

#### Administrateurs

- **Gestion des utilisateurs :** visualisation, modification et suppression des comptes.
- **Modération des contenus :** validation, édition ou suppression des publications.
- **Gestion des commentaires :** surveillance et modération des interactions sociales.
- **Tableau de bord :** statistiques sur l'utilisation de la plateforme et les contenus populaires.

## Choix technologiques

### Frontend

- HTML5, CSS3 et Tailwind CSS pour la structure et le style.
- Blade (Laravel) pour les templates dynamiques.
- JavaScript pour les interactions côté client.

### Backend

- PHP avec le framework Laravel pour une architecture MVC robuste.
- MySQL pour la gestion des données relationnelles.

### Sécurité

- Middleware Laravel pour la protection des routes.
- Validation des formulaires côté serveur afin d'éviter les injections et les données invalides.

## Compatibilité

### Navigateurs compatibles

- Google Chrome
- Mozilla Firefox
- Safari
- Microsoft Edge

### Appareils

- Ordinateurs de bureau
- Tablettes
- Smartphones

## Sécurité et conformité

- **Authentification :** système de connexion sécurisé avec gestion des sessions.
- **Autorisation :** contrôle d'accès basé sur les rôles utilisateur et administrateur.
- **Protection des données :** chiffrement des mots de passe et sécurisation des données sensibles.
- **Conformité :** respect des bonnes pratiques en matière de développement web sécurisé.

## Guide d'installation et d'exécution

Pour installer, configurer et lancer l'application **Impact** en environnement local, suivez les étapes détaillées ci-dessous :

### 1. Prérequis système

- **PHP** >= 8.2 (extensions recommandées : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`)
- **Composer** (gestionnaire de dépendances PHP)
- **Node.js** (v18+ recommandé) et **npm**
- **MySQL** (ou un serveur MariaDB équivalent)

### 2. Accéder au répertoire du projet Laravel

Le code source de l'application se situe dans le sous-dossier `project/`. Ouvrez votre terminal et naviguez dans ce répertoire :

```bash
cd project
```

### 3. Installation des dépendances

Installez les dépendances backend (PHP) et frontend (JavaScript / Tailwind CSS) :

```bash
# Dépendances PHP (Laravel et packages)
composer install

# Dépendances Node.js (Vite, Tailwind CSS, etc.)
npm install
```

### 4. Configuration des variables d'environnement

1. Créez votre fichier d'environnement `.env` à partir du fichier d'exemple :
   ```bash
   # Sur Linux / macOS / Git Bash :
   cp .env.example .env

   # Sur Windows (cmd / PowerShell) :
   copy .env.example .env
   ```

2. Générez la clé de chiffrement de l'application :
   ```bash
   php artisan key:generate
   ```

3. Modifiez le fichier `.env` pour configurer la connexion à votre base de données MySQL :
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=impact_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   > **Note :** Veillez à créer au préalable la base de données (ex: `impact_db`) dans votre serveur MySQL avant de passer à l'étape suivante.

### 5. Migration de la base de données et lien de stockage

1. Exécutez les migrations pour créer toutes les tables nécessaires (`users`, `posts`, `comments`, `media`, `tags`, `albums`, `activities`, etc.) :
   ```bash
   php artisan migrate
   ```

2. Créez le lien symbolique vers le dossier de stockage (obligatoire pour rendre accessibles publiquement les fichiers multimédias uploadés) :
   ```bash
   php artisan storage:link
   ```

### 6. Lancement du projet

Vous pouvez démarrer l'application de deux façons :

- **Méthode 1 (Recommandée - Laravel 12) :**
  Lancez simultanément le serveur de développement Laravel, les files d'attente et le watcher Vite :
  ```bash
  composer run dev
  ```

- **Méthode 2 (Terminaux séparés) :**
  - Dans un premier terminal, démarrez le serveur Laravel :
    ```bash
    php artisan serve
    ```
  - Dans un deuxième terminal, lancez le serveur de développement Vite :
    ```bash
    npm run dev
    ```

- **Méthode 3 (Docker Compose) :**
  Pour exécuter l'application conteneurisée avec Docker Desktop :
  ```bash
  cd project
  docker compose up -d
  ```
  Pour arrêter le conteneur :
  ```bash
  docker compose down
  ```

L'application sera accessible dans votre navigateur à l'adresse : **[http://127.0.0.1:8000](http://127.0.0.1:8000)** (ou `http://localhost:8000`).

### 7. Gestion des comptes et rôle administrateur

- Accédez à la route `/register` pour créer un compte utilisateur.
- Pour tester les fonctionnalités d'administration (`/admin/dashboard`), mettez à jour la colonne `role` de votre enregistrement utilisateur dans la table `users` en lui attribuant la valeur `'admin'`.

## Conclusion

Ce cahier des charges définit les spécifications fonctionnelles et techniques du projet Fil Rouge, une plateforme web dédiée au partage et à la découverte de contenus multimédias. En s'appuyant sur des technologies modernes et une architecture solide, le projet vise à offrir une expérience utilisateur fluide et sécurisée, tout en mettant en valeur les compétences en développement web full-stack.


