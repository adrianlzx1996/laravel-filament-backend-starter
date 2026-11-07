# 🎛️ Laravel Filament Backend Starter

![Laravel](https://img.shields.io/badge/Laravel-13-red)
![Filament](https://img.shields.io/badge/Filament-v5-orange)
![Tailwind](https://img.shields.io/badge/Tailwind-v4-blue)

A clean and opinionated starter template for building an **admin backend panel** using **Laravel 13** and **Filament PHP v5**.

## ✨ Features

- Dedicated backend panel accessible via path or subdomain
- Secure authentication with:
    - Email invitation system on user creation
    - Password renewal required on first login (via [Filament Renew Password](https://github.com/yebor974/filament-renew-password))
    - Multi-factor authentication with App authentication (not required for user)
- User management (Filament Resource) with:
    - [Spatie Laravel Permission](https://github.com/spatie/laravel-permission) for roles & permissions
    - Default roles and policies pre-seeded
    - Backend users only (not intended for frontend customers)
- User profile management (email, name, password, timezone)
- Light & Dark theme switch + empty custom theme ready to be extended
- Database notifications (Filament native support)
- [Logs Viewer](https://github.com/opcodesio/log-viewer) integration (`opcodesio/log-viewer`) — protected by auth & backend role
- [Laravel Horizon](https://laravel.com/docs/horizon) — queued jobs management — protected by auth and backend role
- Timezone management from user profile
- Centralized date display settings (`TextColumn`, `DateTimePicker`)
- Default password policy (min 12 characters, mixed case, numbers, symbols)
- Support for subdomain routing (`backend.test.com`)
- Fully **localized**, default to 🇬🇧 english. Extends with your own languages
- Production-ready Docker setup included (multi-stage build, Nginx, Horizon, scheduler, PostgreSQL, Redis)
- Dev-friendly tools:
    - [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)
    - [PHPStan](https://phpstan.org/) for static analysis
    - [Pint](https://laravel.com/docs/pint) for consistent code formatting

## 🧱 Tech Stack

- Laravel 13
- Filament PHP v5
- TALL Stack (Tailwind v4, Alpine, Laravel, Livewire)
- PHP 8.3+
- PostgreSQL or MySQL supported
- Redis (for queue & cache)
- Laravel Horizon
- Spatie Permissions

## 📁 Structure

- `app/Filament/Backend` → Custom backend panel definition
- `app/Models/User` → Used as backend admin users
- `app/Enums/RolesEnum` → Role management using enums
- `resources/lang/` → Multilingual support (en defined)
- `database/seeders/` → Pre-seeded roles & permissions
- `app/Providers/AppServiceProvider.php` → Common boot config:
    - Filament color scheme
    - Timezone per user
    - Password rules
    - LogViewer protection
- `docker/` → Docker configuration files:
    - `common/php-fpm/` → Shared Dockerfile and PHP config
    - `example/nginx/` → Example Nginx config + self-signed certs for local use

## 🔐 Default Access

After seeding:

- **Role:** `Backend`
- **Permissions:** managed by Spatie
- You can invite other backend users from the Filament panel
- Invited backend users will receive a link to connect and to set their new password (with reset required)

## 🔄 Ready for...

✅ SaaS admin panels  
✅ Internal dashboard  
✅ Multi-role business tools  
❌ Not intended for public-facing user portals

## ⚙️ Configuration & Customizations

This starter provides **opinionated defaults** for faster setup in `AppServiceProvider`:

### **1. Filament Colors**

The backend uses a custom color palette:

```php
FilamentColor::register([
    'danger' => Color::Red,
    'gray' => Color::Zinc,
    'info' => Color::Blue,
    'primary' => Color::Amber,
    'success' => Color::Green,
    'warning' => Color::Amber,
]);
```

### **2. Password Policy**

All user passwords must follow a **strong password policy**:

```php
Password::defaults(function () {
    return Password::min(12)
        ->mixedCase()
        ->numbers()
        ->symbols();
});
```

### **3. Log Viewer Protection**

The `opcodesio/log-viewer` package is restricted to users with the `Backend` role:

```php
Gate::define('viewLogViewer', function (?User $user) {
    if ($user && $user->hasRole(RolesEnum::BACKEND->value)) {
        return true;
    }

    return false;
});
```

### **4. Date & Timezone Management**

All `DateTimePicker` and `TextColumn` components use the authenticated user’s timezone:

```php
DateTimePicker::configureUsing(function (DateTimePicker $component): void {
    $component->timezone(auth()->user() ? auth()->user()->timezone : config('app.timezone'));
});

TextColumn::configureUsing(function (TextColumn $component): void {
    if (in_array($component->getName(), ['created_at', 'updated_at', 'email_verified_at'])) {
        $component->timezone(auth()->user() ? auth()->user()->timezone : config('app.timezone'))
            ->dateTime('d/m/Y H:i');
    }
});
```

### **5. Roles & Permissions**

Roles are seeded using `Spatie/laravel-permission` and defined in `PermissionsRolesTablesSeeder`.

## 🛠 How to Install?

### Without Docker

1. Install dependencies:

```bash
composer install
cp .env.example .env
php artisan key:generate

npm install
npm run build
````

2. Configure your setup (Database, Redis, ...) and your environment variables in `.env` file.

You have to define at least one of these custom env variables to set how to access the backend panel, for example:

* To have backend admin panel access at `backend.yourdomain.com`:

```
FILAMENT_PANELS_BACKEND_DOMAIN=backend.yourdomain.com
FILAMENT_PANELS_BACKEND_PATH=
```

* To have backend admin panel access at `yourdomain.com/backend`:

```
FILAMENT_PANELS_BACKEND_DOMAIN=
FILAMENT_PANELS_BACKEND_PATH=backend
```

3. Run setup command to run migrations, seed roles & permissions, and create your first backend user:

```bash
php artisan backend:setup
```

That’s it! Your **backend admin panel is ready to use**.

### With Docker (recommended)

A `docker-compose.example.yaml` is included as a starting point. It includes PHP-FPM, Nginx, PostgreSQL, Redis, Horizon, and the scheduler, all wired together with healthchecks.

> For a detailed explanation of the Docker setup and production configuration, see:  
> 👉 [Production-Ready Docker Setup for Laravel Filament](https://filamentmastery.com/articles/production-ready-docker-setup-for-laravel-filament/)  
> 👉 [Production-Ready Docker Compose for Laravel Filament](https://filamentmastery.com/articles/production-ready-docker-compose-for-laravel-filament/)

**Quick start:**

1. Copy and adapt the example compose file:

```bash
cp docker-compose.example.yaml docker-compose.yaml
```

2. Generate self-signed SSL certificates for local use (already provided in `docker/example/nginx/certs/`):

```bash
# If you need to regenerate them:
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout docker/example/nginx/certs/selfsigned.key \
    -out docker/example/nginx/certs/selfsigned.crt
```

3. Copy and configure your environment:

```bash
cp .env.docker.example .env
# Edit .env with your database credentials, Redis config, and panel domain/path
```

4. Build and start the containers:

```bash
docker compose up -d --build
```

The `bootstrap` service runs `php artisan migrate --force` automatically before the app starts.

5. Once containers are up, run the setup command to seed roles & permissions and create your first backend user:

```bash
docker compose exec php-fpm php artisan backend:setup
```

6. Access the panel:

```
https://localhost:4443/backend
# or your configured FILAMENT_PANELS_BACKEND_PATH / FILAMENT_PANELS_BACKEND_DOMAIN
```

> ⚠️ Your browser will warn about the self-signed certificate, this is expected in local development. Proceed or add the cert to your trusted certificates.

> ⚠️ Always use `https://` explicitly, accessing `http://localhost:4443` will return an error since Nginx expects SSL on that port.

**Docker structure:**

```
docker/
├── common/
│   └── php-fpm/
│       ├── Dockerfile          ← Multi-stage build (composer → node → php-fpm)
│       └── conf.d/
│           └── php.ini
└── example/
    └── nginx/
        ├── default.conf        ← Nginx config (HTTP→HTTPS redirect + PHP-FPM)
        └── certs/
            ├── selfsigned.crt  ← Self-signed cert for local dev
            └── selfsigned.key
```

> For production deployment, adapt the compose file and Nginx config to your own infrastructure (reverse proxy, real certificates, CI/CD pipeline). See the [GitLab CI guide](https://filamentmastery.com/articles/production-ready-gitlab-ci-pipeline-for-laravel-filament/) for a complete production pipeline example.

## 🧪 Tests

Some basic PEST tests included for:

* Auth flow
* Backend user resource

## 📦 Credits

* [Filament PHP](https://filamentphp.com/)
* [Filament Renew Password](https://github.com/yebor974/filament-renew-password)
* [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar)
* [Laravel Horizon](https://laravel.com/docs/horizon)
* [Laravel Pint](https://laravel.com/docs/pint)
* [Logs Viewer](https://github.com/opcodesio/log-viewer)
* [PHPStan](https://phpstan.org/)
* [Spatie Laravel Permission](https://github.com/spatie/laravel-permission)

## 📄 License

Commercial license. See [LICENSE.md](./LICENSE.md) for details.
