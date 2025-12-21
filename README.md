# Laravel 12 Filament 4 Starter Kit

A production-ready starter kit for building admin panels and internal tools with Laravel 12 and Filament 4. Includes a complete RBAC system, activity logging, and demo components to accelerate your development.

## Why This Starter Kit?

- **Skip the boilerplate** - Authentication, authorization, and user management ready out of the box
- **Learn Filament 4** - Demo cluster showcases forms, tables, widgets, actions, and infolists
- **Production patterns** - Policies, observers, services, and traits following Laravel best practices
- **Modern stack** - Latest versions of Laravel 12, Filament 4, Livewire 3, and Tailwind 4

## Tech Stack

| Component | Version |
|-----------|---------|
| PHP | 8.4 |
| Laravel | 12.x |
| Filament | 4.x |
| Livewire | 3.x |
| Tailwind CSS | 4.x |
| Pest | 4.x |
| PHPStan | Level 5 |

## Features

### Authentication & Authorization
- **Filament Authentication** - Login with session-based auth
- **Role-Based Access Control** - Granular permissions with hierarchical access levels
- **Super Admin Role** - Bypass all permission checks
- **Policy-Based Authorization** - Laravel policies for all resources

### User Management
- **User CRUD** - Create, edit, delete users with role assignments
- **User Status** - Active/inactive status controls panel access
- **Avatar Support** - User profile images
- **Blameable Records** - Track who created/updated records

### Activity Logging
- **Audit Trail** - Log all user actions automatically
- **IP & User Agent** - Capture request metadata
- **Polymorphic Relations** - Link logs to any model
- **Login/Logout Tracking** - Authentication events logged

### Demo Showcase
- **Form Inputs** - All Filament input components
- **Form Layouts** - Sections, tabs, grids, wizards
- **Tables** - Columns, filters, actions, bulk actions
- **Widgets** - Stats, charts (line, bar, pie)
- **Actions** - Modal forms, confirmations, notifications
- **Infolists** - Read-only data display

## Requirements

- PHP 8.4+
- Composer
- Node.js 20+ & pnpm
- SQLite (default) or MySQL/PostgreSQL

## Installation

### Using Laravel Installer (Recommended)

```bash
laravel new my-app --using=abitbt/filament
```

The installer will automatically set up everything for you.

### Manual Installation

```bash
# Create project
composer create-project abitbt/filament my-app
cd my-app

# Or clone the repository
git clone https://github.com/abitbt/filament.git my-app
cd my-app
composer install

# Setup
pnpm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
pnpm run build
```

### Start Development Server

```bash
composer run dev
```

Access the admin panel at: `http://localhost:8000/admin`

### Default Credentials

| Field | Value |
|-------|-------|
| Email | `admin@example.com` |
| Password | `password` |

> **Note:** Change these credentials immediately in production environments.

## Development Commands

```bash
# Development server with hot reload
composer run dev

# Code formatting
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse

# Run all tests
unset APP_ENV && php artisan test

# Run specific test
unset APP_ENV && php artisan test --filter=UserResourceTest
```

## Project Structure

```
app/
├── Enums/
│   ├── Permission.php         # Permission definitions with groups
│   ├── UserStatus.php         # Active/Inactive status
│   └── ActivityEvent.php      # Log event types
├── Filament/
│   ├── Clusters/Demo/         # Demo pages & widgets
│   ├── Pages/Dashboard.php    # Main dashboard
│   ├── Resources/
│   │   ├── UserResource/      # User management
│   │   ├── RoleResource/      # Role management
│   │   └── ActivityLogResource/
│   └── Widgets/               # Dashboard widgets
├── Models/
│   ├── Concerns/
│   │   ├── Blamable.php       # created_by/updated_by trait
│   │   └── HasPermissions.php # Permission checking trait
│   ├── User.php
│   ├── Role.php
│   ├── Permission.php
│   └── ActivityLog.php
├── Observers/                 # Model event observers
├── Policies/                  # Authorization policies
└── Services/
    ├── ActivityLogger.php     # Logging service
    └── PermissionRegistrar.php
```

## Permission System

Permissions use a hierarchical access model:

| Level | Permission | Grants |
|-------|------------|--------|
| 0 | None | No access |
| 1 | Read | View records |
| 2 | Write | Read + Create/Edit |
| 3 | Delete | Read + Write + Delete |

### Available Permissions

| Group | Permissions |
|-------|-------------|
| Users | `users.read`, `users.write`, `users.delete` |
| Roles | `roles.read`, `roles.write`, `roles.delete` |
| Activity Logs | `activity_logs.read`, `activity_logs.write`, `activity_logs.delete` |

### Adding New Permissions

1. Add cases to `app/Enums/Permission.php`
2. Update `getGroup()` and `getGroupIcon()` methods
3. Create a policy in `app/Policies/`
4. Run `php artisan db:seed --class=PermissionSeeder`

## Database Schema

```
users
├── id, name, email, password
├── avatar, status (enum)
├── role_id (foreign key)
├── created_by, updated_by (blameable)
└── timestamps

roles
├── id, name, slug, description
├── is_default (boolean)
└── timestamps

permissions
├── id, name, group, description
└── timestamps

role_permission (pivot)
├── role_id
└── permission_id

activity_logs
├── id, user_id
├── subject_type, subject_id (polymorphic)
├── event, description, properties (json)
├── ip_address, user_agent
└── created_at
```

## Customization

### Panel Configuration

Edit `app/Providers/Filament/AdminPanelProvider.php`:

```php
return $panel
    ->brandName('Your App Name')
    ->colors(['primary' => Color::Blue])
    ->path('admin')  // Change URL path
    ->spa()          // SPA mode (remove for traditional)
    ->sidebarCollapsibleOnDesktop();
```

### Adding Resources

```bash
# Generate a new resource
php artisan make:filament-resource Post --generate

# Generate with soft deletes
php artisan make:filament-resource Post --generate --soft-deletes
```

### Adding Widgets

```bash
# Stats widget
php artisan make:filament-widget PostStats --stats-overview

# Chart widget
php artisan make:filament-widget PostsChart --chart
```

## Testing

Tests use Pest 4 and cover all resources:

```bash
# Run all tests
unset APP_ENV && php artisan test

# Run with coverage
unset APP_ENV && php artisan test --coverage

# Run specific test file
unset APP_ENV && php artisan test tests/Feature/UserResourceTest.php
```

## Filament 4 Key Changes

This starter kit uses Filament 4 conventions:

| Filament 3 | Filament 4 |
|------------|------------|
| `Form $form` | `Schema $schema` |
| `$form->schema([])` | `$schema->components([])` |
| `form()` on actions | `schema()` |
| `Filament\Forms\Form` | `Filament\Schemas\Schema` |
| `Filament\Forms\Get` | `Filament\Schemas\Components\Utilities\Get` |

## Contributing

1. Fork the repository
2. Create a feature branch
3. Run `./vendor/bin/pint` and `./vendor/bin/phpstan analyse`
4. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
