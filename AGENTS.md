# AGENTS.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

This is a Laravel 12 HR Management System using PHP 8.2+, PostgreSQL as the primary database, and Pest for testing. The frontend uses Vite with Tailwind CSS v4.

## Development Commands

### Setup
```bash
# Initial project setup (runs composer install, npm install, env setup, key generation, migrations, and asset build)
composer setup

# Start development environment (runs server, queue worker, and Vite concurrently)
composer dev
```

### Server
```bash
# Start Laravel development server only
php artisan serve

# Start Vite dev server for hot module replacement
npm run dev
```

### Database
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration (drops all tables and re-runs)
php artisan migrate:fresh

# Run migrations with seeders
php artisan migrate --seed
```

### Testing
```bash
# Run all tests (uses Pest)
composer test
# OR
php artisan test

# Run specific test file
php artisan test tests/Feature/ExampleTest.php

# Run tests with coverage
php artisan test --coverage
```

### Code Quality
```bash
# Format code with Laravel Pint
vendor/bin/pint

# Fix code style issues
vendor/bin/pint --repair
```

### Assets
```bash
# Build assets for production
npm run build
```

### Queue & Background Jobs
```bash
# Run queue worker (queue connection: database)
php artisan queue:listen

# Run queue worker with retry limit
php artisan queue:listen --tries=1
```

### Cache & Config
```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Cache configuration for production
php artisan config:cache
```

## Architecture

### Directory Structure
- **app/Http/Controllers**: HTTP request handlers
- **app/Models**: Eloquent ORM models
- **app/Providers**: Service providers for dependency injection and bootstrapping
- **database/migrations**: Database schema migrations (uses PostgreSQL in production, SQLite in-memory for testing)
- **database/factories**: Model factories for testing and seeding
- **database/seeders**: Database seeders
- **resources/views**: Blade templates
- **resources/css**: Stylesheets (processed by Vite + Tailwind CSS v4)
- **resources/js**: JavaScript files (processed by Vite)
- **routes/web.php**: Web routes
- **routes/console.php**: Artisan console commands
- **tests/Feature**: Feature tests (integration tests)
- **tests/Unit**: Unit tests

### Testing Framework
This project uses **Pest** (not PHPUnit) as configured in composer.json. Tests are organized into Feature and Unit directories. The test configuration in phpunit.xml uses SQLite in-memory database for faster test execution.

### Database
- **Production/Development**: PostgreSQL (DB_CONNECTION=pgsql)
- **Testing**: SQLite in-memory (configured in phpunit.xml)
- Session, cache, and queue all use database drivers by default

### Frontend Stack
- **Vite**: Asset bundler (configured in vite.config.js)
- **Tailwind CSS v4**: Styling framework with Vite plugin
- **Entry points**: resources/css/app.css and resources/js/app.js
- Hot module replacement enabled during development
- Framework views are ignored in the Vite watcher for performance

### Key Configuration
- **PHP Version**: 8.2+
- **Laravel Version**: 12.x
- **Default DB**: PostgreSQL (hr_management_system database)
- **Session Driver**: database
- **Queue Connection**: database
- **Cache Store**: database
- **Mail**: Logs to file in development (MAIL_MAILER=log)

## Important Notes

When running migrations or seeders, ensure the PostgreSQL database exists and credentials in .env are correct. For local development, you may need to create the database manually:

```bash
# Connect to PostgreSQL and create database
psql -U postgres
CREATE DATABASE hr_management_system;
```

When making database changes, always create migrations rather than modifying the database directly to maintain version control and team consistency.
