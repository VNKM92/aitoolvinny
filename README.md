# SaaS Multi-Tenant CMS (Laravel 12 + Livewire + Tailwind CSS v4)

A SaaS-grade, production-ready multi-tenant Content Management System built with **Laravel 12**, **PHP 8.4** (compatible with PHP 8.2+), **MySQL/MariaDB**, **Tailwind CSS v4**, **Livewire**, and **Alpine.js**.

The platform allows a single Super Admin to provision, manage, and scale unlimited websites from a single central dashboard. Each tenant site supports custom domain mapping, multi-language localization, automatic SEO optimization, and configurable Google AdSense placements.

---

## 🏗️ Architecture Design

The platform uses a **Single Database (Shared Schema) Multi-Tenant Model** with **Global Query Scoping**. 

```
                               Incoming Request
                                      │
                                      ▼
                        [IdentifyTenant Middleware]
                                      │
       ┌──────────────────────────────┴──────────────────────────────┐
       ▼ (Central Hostname)                                          ▼ (Tenant Hostname)
 SaaS Central App                                             Tenant Resolved Context
  ├── Landing Page (/)                                         ├── Database: Scoped via TenantScope
  └── Super Admin Dashboard (/admin)                           ├── Locale: Configured via SetLocale
       ├── Website Provisioner                                 ├── Public Frontend (Theme Wrapper)
       └── Custom Domain Mapping                               └── Tenant Admin Dashboard (/admin)
                                                                    ├── Category Editor
                                                                    ├── Blog Post Composer
                                                                    ├── Static Page Creator
                                                                    └── AdSense Configuration
```

### Key Components

1. **Tenant Resolution**: Resolves tenants at the middleware level (`IdentifyTenant.php`) by matching the request domain against custom domains (`tenant_domains`) or fallback subdomains (`tenants`). The resolved tenant is cached for maximum speed.
2. **Database Scoping**: Scoped models implement the `BelongsToTenant` trait. A global query scope (`TenantScope.php`) intercepts all Eloquent commands to inject `where('tenant_id', $activeTenantId)` conditions dynamically.
3. **Data Access Isolation**: Repositories abstract raw database operations, keeping controllers/Livewire modules decoupled from database queries.
4. **Translations (Localization)**: Localized fields (Titles, Contents, SEO Meta) are stored as JSON maps (e.g., `{"en": "Hello", "es": "Hola"}`). The system sets locales dynamically based on URL prefixes (e.g. `/es/...`).
5. **Caching Layer**: Caches sitemaps, static pages, and blog queries. Caches are invalidated instantly on content CRUD updates.

---

## 📁 Directory Structure

Key files and directories created for this application:

```text
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── SitemapController.php     # Dynamic XML sitemap generator per tenant
│   │   │   └── TenantController.php      # Main public controller for tenant sites
│   │   └── Middleware/
│   │       ├── EnsureSuperAdmin.php      # Guard for SaaS Super Admin routes
│   │       ├── IdentifyTenant.php        # Hostname resolver and tenant context initiator
│   │       └── SetLocale.php             # Route-prefix language configuration
│   ├── Livewire/
│   │   ├── Admin/
│   │   │   ├── Categories.php            # Multilingual category management
│   │   │   ├── Dashboard.php             # adapts to Super Admin or Tenant Admin
│   │   │   ├── Pages.php                 # Static page management
│   │   │   ├── Posts.php                 # Localized blog posts + image upload
│   │   │   └── Settings.php              # Theme, locales, and AdSense slot config
│   │   └── Auth/
│   │       └── Login.php                 # Unified authentication module
│   ├── Models/
│   │   ├── Scopes/
│   │   │   └── TenantScope.php           # Scoping Eloquent query builder
│   │   ├── Tenant.php                    # Central Tenant website model
│   │   ├── TenantDomain.php              # Mapping custom hostnames to tenants
│   │   └── Category, Post, Page, Menu... # Tenant-scoped content models
│   ├── Providers/
│   │   └── AppServiceProvider.php        # Registering TenantManager and Repository binds
│   ├── Repositories/
│   │   ├── Contracts/                    # Repository interface declarations (SOLID)
│   │   └── Eloquent/                     # Concrete Eloquent implementations
│   ├── Services/
│   │   ├── PostService, PageService.php  # Handles business logic and cache maps
│   │   ├── SEOService.php                # Generates HTML meta tags & JSON-LD schemas
│   │   └── TenantManager.php             # In-memory tenant registry and cache helper
│   └── Traits/
│       └── BelongsToTenant.php           # Trait applied to all tenant models
├── config/
│   └── tenancy.php                       # Central domain mapping configuration
├── database/
│   ├── migrations/                       # Correctly ordered DB schemas
│   └── seeders/
│       └── DatabaseSeeder.php            # Pre-populates Super Admin and sample Tenant
├── resources/
│   ├── css/app.css                       # Tailwind CSS v4 assets
│   └── views/
│       ├── components/
│       │   └── tenant-layout.blade.php   # Public tenant site layout wrapper
│       ├── central_welcome.blade.php     # central SaaS landing page view
│       └── livewire/                     # Blade layouts for all Livewire components
└── routes/
    └── web.php                           # SaaS domain-separated routing definitions
```

---

## 🗄️ Database Schemas

### Central SaaS Tables
- **`tenants`**: `id`, `name`, `subdomain` (unique), `is_active`, `default_locale`, `supported_locales` (JSON), `settings` (JSON for branding/AdSense), `timestamps`.
- **`tenant_domains`**: `id`, `tenant_id` (FK), `domain` (unique), `is_primary`, `timestamps`.

### Shared Scoped Tables
- **`users`**: `id`, `tenant_id` (nullable, index), `name`, `email` (unique), `password`, `role` (`super_admin` vs `tenant_admin`), `remember_token`, `timestamps`.
- **`categories`**: `id`, `tenant_id` (FK), `name` (JSON), `slug` (unique per tenant), `timestamps`.
- **`posts`**: `id`, `tenant_id` (FK), `category_id` (nullable FK), `title` (JSON), `slug` (unique per tenant), `content` (JSON), `featured_image`, `status` (`draft` / `published`), `meta_title` (JSON), `meta_description` (JSON), `adsense_enabled`, `published_at`, `timestamps`.
- **`pages`**: `id`, `tenant_id` (FK), `title` (JSON), `slug` (unique per tenant), `content` (JSON), `status`, `meta_title` (JSON), `meta_description` (JSON), `timestamps`.

---

## 🚀 Setup & Installation

### 1. Configure the Environment
Ensure your local environment runs PHP 8.2+ and MariaDB/MySQL is active (the default XAMPP setup uses port `3307`).

Modify your `.env` file database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=aitoolvinny_cms
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Map Hostnames (hosts file)
For development, map local mock hostnames inside your operating system's hosts file (e.g. `C:\Windows\System32\drivers\etc\hosts` on Windows):
```text
127.0.0.1 central.local
127.0.0.1 devblog.localhost
```

### 3. Run Migrations and Seeders
Execute the database setup:
```bash
php artisan migrate:fresh --seed
```
This drops existing tables, creates the schemas, and seeds default administrators.

### 4. Build Frontend Assets
Compile Tailwind CSS v4 assets:
```bash
npm install
npm run build
```

### 5. Serve the Site
Launch the PHP development server:
```bash
php artisan serve --port=80
```

---

## 🔑 Default Credentials

- **Central SaaS Admin Panel**: Visit `http://central.local/login`
  - **Email**: `admin@saas.com`
  - **Password**: `password`
  - *Provides: Super Admin control to create new websites and map hostnames.*

- **Tenant Blog Dashboard**: Visit `http://devblog.localhost/login`
  - **Email**: `editor@devblog.com`
  - **Password**: `password`
  - *Provides: Scoped Tenant access to write articles, create pages, manage taxonomy, modify languages, and configure Google AdSense slot IDs.*

---

## 🧪 Testing & Code Quality

Automated feature tests verify:
- **Subdomain and custom domain tenant resolution**.
- **Database isolation** (ensuring Tenant A query boundaries prevent seeing Tenant B data).
- **Localized path resolution** (matching URL segments like `/es` and setting matching locales).

Run tests at any time using:
```bash
php artisan test
```
