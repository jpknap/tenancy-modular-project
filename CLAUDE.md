# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Development (runs server, queue, logs, and vite concurrently)
composer dev

# Run all tests
composer test
# or
php artisan test

# Run a single test file
php artisan test tests/Unit/SomeTest.php
# Run a specific test method
php artisan test --filter=testMethodName

# Static analysis (PHPStan level 5)
composer test-phpstan
# or
./vendor/bin/phpstan analyse

# Check coding style (ECS)
composer check-style
# Fix coding style automatically
composer fix-style

# Run all quality checks (style + phpstan)
composer analyze

# Frontend assets
npm run dev    # development with HMR
npm run build  # production build
```

## Architecture

This is a **multi-tenant Laravel 13 application** using `stancl/tenancy` with a custom **modular project system** where each "project" runs under a different domain or tenant.

### Project System

The central concept is a `ProjectInterface` (`app/Contracts/ProjectInterface.php`). Each project (Landlord, ActivitiesBoard, SportCompetition) implements this interface and lives under `app/Projects/{ProjectName}/`.

`ProjectManager` (`app/ProjectManager.php`) is a static registry that maps domain prefixes to project classes and tracks the currently active project. It is populated at boot via `ProjectInitService`.

Routes are **not declared manually** — they are derived at runtime from PHP 8 attributes on controller methods. `EndpointProcessor` (`app/Services/EndpointProcessor.php`) uses reflection to scan controllers registered in `config/projects.php` and builds `Endpoint` DTOs from `#[Route]`, `#[RoutePrefix]`, `#[Middleware]`, and `#[Where]` attributes. `web.php` then registers these endpoints dynamically.

### Project Structure (per project)

```
app/Projects/{Name}/
├── {Name}Project.php          # Implements ProjectInterface, calls init()
├── Providers/{Name}ServiceProvider.php  # Registers repositories & services
├── Enums/Routes.php           # Route name constants
├── Models/                    # Eloquent models
├── Repositories/              # Extend BaseRepository
├── Services/Model/            # Business logic services
├── Adapters/Admin/            # Extend AdminBaseAdapter (CRUD config)
├── FormRequests/              # Extend BaseFormRequest (form validation + builder)
└── Http/Controller/           # Controllers with PHP attribute routing
```

### Common (Shared) Layer

`app/Common/` contains all shared infrastructure:

- **Admin system** (`Common/Admin/`): `AdminBaseAdapter` + `AdminController` provide a generic CRUD flow. Each admin adapter defines `getListViewConfig()`, `getCreateViewConfig()`, `getEditViewConfig()`, `getDeleteViewConfig()` using config builder objects (`ListViewConfig`, `CreateViewConfig`, etc.). `FormBuilder` (fluent builder) constructs form field definitions rendered by Blade views.
- **Repository** (`Common/Repository/`): `BaseRepository` with standard CRUD methods. `RepositoryManager` acts as a service locator registered per project — access repositories via `$manager->get(ModelClass::class)`.
- **Services** (`Common/Repository/Service/TransactionService`): wraps DB transactions for service-layer operations.

### Adding a New Project

1. Create `app/Projects/{Name}/{Name}Project.php` implementing `ProjectInterface`.
2. Create a `ServiceProvider` that registers repositories in `RepositoryManager` and binds services.
3. Add controllers with `#[RoutePrefix]`, `#[Route]`, and `#[Middleware]` attributes.
4. Create `Adapters/Admin/` classes extending `AdminBaseAdapter` for CRUD screens.
5. Register the project in `ProjectManager::$projects` and `config/projects.php`.

### Route Naming Convention

Routes follow the pattern `{projectPrefix}.{classPrefix}.{actionName}`, e.g. `landlord.admin.tenants.list`. The project prefix comes from `ProjectInterface::getPrefix()`, class prefix from `#[RoutePrefix]` (inherited through parent classes), and action name from `#[Route(..., name: 'list')]`.

### Testing

Tests use SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`). The `TENANCY_CENTRAL_CONNECTION` is also set to `sqlite` for tenancy tests.
