# Impact 🎨✨
### *Modern Visual Discovery & Creative Media Bookmarking Platform*

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-6.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![Pest](https://img.shields.io/badge/Tests-Pest_PHP-F43F5E?style=for-the-badge&logo=php&logoColor=white)](https://pestphp.com)
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

---

## 🌟 Overview

**Impact** is a full-stack visual discovery and bookmarking platform inspired by Pinterest and Unsplash. Built with modern web standards, Impact empowers creators, photographers, and visual enthusiasts to share high-resolution media, curate themed albums, explore trending design tags, and cultivate creative networks.

The platform is engineered following enterprise PHP/Laravel standards, featuring granular authorization policies, eager-loaded Eloquent relationships to eliminate N+1 bottlenecks, an automated Vite asset bundling pipeline, and interactive AJAX social graph interactions.

---

## 🚀 Key Features

- **Responsive Masonry Feed**: Dynamic Pinterest-style multi-column layout with smooth image cards, hover actions, and pagination.
- **Dynamic Follow / Unfollow System**: Zero-reload AJAX follow mechanics that live-sync follower counts across posts and profile views.
- **Rich Media Sharing**: Support for multi-image albums, animated media, high-resolution photography, and embedded video previews.
- **Role-Based Access Control (RBAC)**:
  - **Creators/Users**: Publish posts, organize albums, bookmark private favorites, leave comments, and customize profiles.
  - **Administrators**: Moderation suite to activate/deactivate accounts, audit archived posts, manage comments, and review platform telemetry.
- **Strict Authorization & IDOR Protection**: Enforced with Laravel Policies (`PostPolicy`, `UserPolicy`) and custom middleware guards (`PostStatusMiddleware`, `AdminMiddleware`).
- **Tag Discovery & Search Engine**: Fast multi-tag categorization with full-text search across titles, descriptions, and creator handles.
- **Curated Collections**: Create public/private albums and bookmark inspiration with instant saved collections.

---

## 🏗️ Architecture & Engineering Highlights

```
project/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Resourceful & single-responsibility controllers
│   │   │   ├── AuthController.php
│   │   │   ├── PostController.php
│   │   │   ├── FollowController.php
│   │   │   └── UserController.php
│   │   ├── Middleware/        # Route-level security guards (UserStatus, Admin, PostStatus)
│   │   └── Requests/          # Strict form request validations (PostRequest, RegisterRequest, LoginRequest)
│   ├── Models/                # Clean singular Eloquent models with relationship cascades & accessors
│   │   ├── Post.php
│   │   ├── Media.php
│   │   ├── User.php
│   │   └── Comment.php
│   └── Policies/              # Gate definitions ensuring ownership security
├── database/
│   ├── factories/             # State-driven factories for automated testing
│   ├── migrations/            # Clean relational schema with indexed foreign keys
│   └── seeders/               # Comprehensive DatabaseSeeder with realistic demo data
└── tests/
    └── Feature/               # Pest test suites covering Auth, Posts, Policies, and Follow system
```

### Technical Highlights:
1. **Zero N+1 Query Overhead**: Feed and profile queries utilize compound eager-loading (`with(['mediaContent', 'tags', 'user'])`) and database-level aggregate preloads (`withCount(['followers', 'following'])`).
2. **Unified Media & Avatar Resolution**: Seamless accessors (`avatar_url`, `url`) dynamically detect external CDNs, cloud storage, or local filesystem links without view-layer hacks.
3. **Resilient Asset Pipeline**: Completely decoupled from third-party CDN scripts, bundled via modern **Vite 6** and **Tailwind CSS 4**.

---

## 🛠️ Tech Stack

| Layer | Technologies |
|---|---|
| **Backend Framework** | Laravel 12.x (PHP 8.2+) |
| **Database** | MySQL / MariaDB (or SQLite for testing) |
| **Frontend & Styling** | Blade Templates, Tailwind CSS 4, Alpine.js, FontAwesome 6 |
| **Build Tooling** | Vite 6, PostCSS, Axios |
| **Test Framework** | Pest PHP & PHPUnit |

---

## ⚡ Quickstart Guide

### Prerequisites
- **PHP** >= 8.2 with PDO, OpenSSL, and Mbstring extensions
- **Composer** >= 2.x
- **Node.js** >= 18.x & **NPM**
- **MySQL** / **MariaDB**

### 1. Clone & Enter Repository
```bash
git clone https://github.com/Abdelilah-Ajjouqa/Impact.git
cd Impact/project
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```
*Configure your database credentials in `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).*

### 4. Run Migrations & Rich Seeders
```bash
php artisan migrate --seed
php artisan storage:link
```

### 5. Build Assets & Start Development Server
```bash
npm run build
php artisan serve
```

Visit [`http://127.0.0.1:8000`](http://127.0.0.1:8000) in your browser.

---

## 🔑 Demo Accounts

The database seeder pre-populates rich accounts, posts, tags, comments, and social graph links:

| Role | Email | Password | Access / Capabilities |
|---|---|---|---|
| **Administrator** | `admin@impact.test` | `password` | Full moderation dashboard, user management, post archiving |
| **Demo Creator** | `demo@impact.test` | `password` | Creator profile, albums, followers, saved posts |
| **Community Creator** | `elena@impact.test` | `password` | Sample creator account with photography posts |

---

## 🧪 Automated Testing

Impact includes a comprehensive suite of automated tests powered by **Pest PHP**:

```bash
# Run all automated tests
php artisan test

# Run specific feature tests
php artisan test --filter=PostTest
php artisan test --filter=FollowTest
php artisan test --filter=AuthTest
```

### Test Coverage Highlights:
- ✅ **Authentication**: Registration validation, credential verification, session handling, logout.
- ✅ **Authorization & Security**: Guest boundaries, IDOR prevention on post edit/update/destroy, private post isolation.
- ✅ **Social Graph**: Follow toggle mechanics, self-follow prohibition (422), unauthenticated rejection.

---

## 📁 Academic & Design Specifications

Academic project deliverables and planning documentation are preserved in their respective directories:
- **Project Specifications**: [`Cahier de charge/`](Cahier%20de%20charge/)
- **Architecture & UML**: [`uml/1.drawio`](uml/1.drawio)
- **Presentation**: [`presentation/link-to-canva.txt`](presentation/link-to-canva.txt)

---

## 📄 License

This project is open-sourced software licensed under the [MIT License](LICENSE).
